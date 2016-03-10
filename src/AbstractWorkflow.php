<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Log\LogFactory;
use OldTown\PropertySet\PropertySetInterface;
use OldTown\PropertySet\PropertySetManager;
use OldTown\Workflow\Config\ConfigurationInterface;
use OldTown\Workflow\Config\DefaultConfiguration;
use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\InvalidActionException;
use OldTown\Workflow\Exception\InvalidArgumentException;
use OldTown\Workflow\Exception\InvalidEntryStateException;
use OldTown\Workflow\Exception\InvalidRoleException;
use OldTown\Workflow\Exception\StoreException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Loader\ActionDescriptor;
use OldTown\Workflow\Loader\PermissionDescriptor;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Spi\SimpleWorkflowEntry;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use Psr\Log\LoggerInterface;
use SplObjectStorage;
use OldTown\Workflow\TransientVars\BaseTransientVars;
use ReflectionClass;
use ArrayObject;
use OldTown\Workflow\Engine\EngineManagerInterface;
use OldTown\Workflow\Engine\EngineManager;



/**
 * Class AbstractWorkflow
 *
 * @package OldTown\Workflow
 */
abstract class  AbstractWorkflow implements WorkflowInterface
{
    /**
     * @var string
     */
    const CURRENT_STEPS = 'currentSteps';

    /**
     * @var string
     */
    const HISTORY_STEPS = 'historySteps';

    /**
     * @var WorkflowContextInterface
     */
    protected $context;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;


    /**
     * @var TypeResolverInterface
     */
    protected $typeResolver;

    /**
     * Логер
     *
     * @var LoggerInterface
     */
    protected $log;

    /**
     * Резолвер для создания провайдеров отвечающих за исполнение функций, проверку условий, выполнение валидаторов и т.д.
     *
     * @var string
     */
    protected $defaultTypeResolverClass = TypeResolver::class;

    /**
     * Карта переходов состояния процесса workflow
     *
     * @var null|array
     */
    protected $mapEntryState;

    /**
     * Менеджер движков
     *
     * @var EngineManagerInterface
     */
    protected $engineManager;

    /**
     * AbstractWorkflow constructor.
     *
     * @throws InternalWorkflowException
     */
    public function __construct()
    {
        $this->initLoger();
        $this->initMapEntryState();
    }

    /**
     * Устанавливает менеджер движков
     *
     * @return EngineManagerInterface
     */
    public function getEngineManager()
    {
        if ($this->engineManager) {
            return $this->engineManager;
        }
        $this->engineManager = new EngineManager($this);

        return $this->engineManager;
    }

    /**
     * Возвращает менеджер движков
     *
     * @param EngineManagerInterface $engineManager
     *
     * @return $this
     */
    public function setEngineManager(EngineManagerInterface $engineManager)
    {
        $this->engineManager = $engineManager;

        return $this;
    }

    /**
     * Инициация карты переходов состояния процесса workflow
     */
    protected function initMapEntryState()
    {
        $this->mapEntryState = [
            WorkflowEntryInterface::COMPLETED => [
                WorkflowEntryInterface::ACTIVATED => WorkflowEntryInterface::ACTIVATED
            ],
            WorkflowEntryInterface::CREATED => [],
            WorkflowEntryInterface::ACTIVATED => [
                WorkflowEntryInterface::CREATED => WorkflowEntryInterface::CREATED,
                WorkflowEntryInterface::SUSPENDED => WorkflowEntryInterface::SUSPENDED,

            ],
            WorkflowEntryInterface::SUSPENDED => [
                WorkflowEntryInterface::ACTIVATED => WorkflowEntryInterface::ACTIVATED
            ],
            WorkflowEntryInterface::KILLED => [
                WorkflowEntryInterface::SUSPENDED => WorkflowEntryInterface::SUSPENDED,
                WorkflowEntryInterface::ACTIVATED => WorkflowEntryInterface::ACTIVATED,
                WorkflowEntryInterface::CREATED => WorkflowEntryInterface::CREATED
            ]
        ];
    }

    /**
     * Инициализация системы логирования
     *
     * @throws InternalWorkflowException
     */
    protected function initLoger()
    {
        try {
            $this->log = LogFactory::getLog();
        } catch (\Exception $e) {
            throw new InternalWorkflowException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Инициализация workflow. Workflow нужно иницаилизровать прежде, чем выполнять какие либо действия.
     * Workflow может быть инициализированно только один раз
     *
     * @param string $workflowName Имя workflow
     * @param integer $initialAction Имя первого шага, с которого начинается workflow
     * @param TransientVarsInterface $inputs Данные введеные пользователем
     *
     * @return integer
     *
     * @throws InternalWorkflowException
     * @throws InvalidActionException
     * @throws InvalidRoleException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws InvalidArgumentException
     * @throws WorkflowException
     */
    public function initialize($workflowName, $initialAction, TransientVarsInterface $inputs = null)
    {
        try {
            $initialAction = (integer)$initialAction;

            $wf = $this->getConfiguration()->getWorkflow($workflowName);

            $store = $this->getPersistence();

            $entry = $store->createEntry($workflowName);

            $ps = $store->getPropertySet($entry->getId());


            if (null === $inputs) {
                $inputs = $this->transientVarsFactory();
            }

            $engineManager = $this->getEngineManager();

            $transientVars = $inputs;
            $inputs = clone $transientVars;

            $engineManager->getDataEngine()->populateTransientMap($entry, $transientVars, $wf->getRegisters(), $initialAction, new ArrayObject(), $ps);

            if (!$this->canInitializeInternal($workflowName, $initialAction, $transientVars, $ps)) {
                $this->context->setRollbackOnly();
                $errMsg = 'You are restricted from initializing this workflow';
                throw new InvalidRoleException($errMsg);
            }

            $action = $wf->getInitialAction($initialAction);

            if (null === $action) {
                $errMsg = sprintf('Invalid initial action id: %s', $initialAction);
                throw new InvalidActionException($errMsg);
            }

            $currentSteps = new SplObjectStorage();
            $transitionManager = $engineManager->getTransitionEngine();
            $transitionManager->transitionWorkflow($entry, $currentSteps, $store, $wf, $action, $transientVars, $inputs, $ps);

            $entryId = $entry->getId();
        } catch (WorkflowException $e) {
            $this->context->setRollbackOnly();
            throw new InternalWorkflowException($e->getMessage(), $e->getCode(), $e);
        }

        return $entryId;
    }

    /**
     * @param $id
     * @param $inputs
     *
     * @return array
     *
     */
    public function getAvailableActions($id, TransientVarsInterface $inputs = null)
    {
        try {
            $store = $this->getPersistence();
            $entry = $store->findEntry($id);

            if (null === $entry) {
                $errMsg = sprintf(
                    'Не существует экземпляра workflow c id %s',
                    $id
                );
                throw new InvalidArgumentException($errMsg);
            }

            if (WorkflowEntryInterface::ACTIVATED === $entry->getState()) {
                return [];
            }

            $wf = $this->getConfiguration()->getWorkflow($entry->getWorkflowName());

            $l = [];
            $ps = $store->getPropertySet($id);

            $transientVars = $inputs;
            if (null === $transientVars) {
                $transientVars = $this->transientVarsFactory();
            }

            $currentSteps = $store->findCurrentSteps($id);

            $engineManager =  $this->getEngineManager();
            $engineManager->getDataEngine()->populateTransientMap($entry, $transientVars, $wf->getRegisters(), 0, $currentSteps, $ps);

            $globalActions = $wf->getGlobalActions();

            $conditionsEngine = $engineManager->getConditionsEngine();
            foreach ($globalActions as $action) {
                $restriction = $action->getRestriction();
                $conditions = null;

                $transientVars['actionId'] = $action->getId();

                if (null !== $restriction) {
                    $conditions = $restriction->getConditionsDescriptor();
                }

                $flag = $conditionsEngine->passesConditionsByDescriptor($wf->getGlobalConditions(), $transientVars, $ps, 0) && $conditionsEngine->passesConditionsByDescriptor($conditions, $transientVars, $ps, 0);
                if ($flag) {
                    $l[] = $action->getId();
                }
            }

            foreach ($currentSteps as $currentStep) {
                $availableActionsForStep = $this->getAvailableActionsForStep($wf, $currentStep, $transientVars, $ps);
                foreach ($availableActionsForStep as $actionId) {
                    $l[] = $actionId;
                }
            }


            return array_unique($l);
        } catch (\Exception $e) {
            $errMsg = 'Ошибка проверки доступных действий';
            $this->getLog()->error($errMsg, [$e]);
        }

        return [];
    }

    /**
     * Создает хранилище переменных
     *
     * @param $class
     *
     * @return TransientVarsInterface
     */
    protected function transientVarsFactory($class = BaseTransientVars::class)
    {
        $r = new \ReflectionClass($class);
        return $r->newInstance();
    }

    /**
     *
     *
     * Осуществляет переходл в новое состояние, для заданного процесса workflow
     *
     * @param integer $entryId id запущенного процесса workflow
     * @param integer $actionId id действия, доступного та текущем шаеге процессса workflow
     * @param TransientVarsInterface $inputs Входные данные для перехода
     *
     * @return void
     *
     * @throws WorkflowException
     * @throws InvalidActionException
     * @throws InvalidArgumentException
     * @throws InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function doAction($entryId, $actionId, TransientVarsInterface $inputs = null)
    {
        $actionId = (integer)$actionId;
        if (null === $inputs) {
            $inputs = $this->transientVarsFactory();
        }
        $transientVars = $inputs;
        $inputs = clone $transientVars;

        $store = $this->getPersistence();
        $entry = $store->findEntry($entryId);

        if (WorkflowEntryInterface::ACTIVATED !== $entry->getState()) {
            return;
        }

        $wf = $this->getConfiguration()->getWorkflow($entry->getWorkflowName());

        $currentSteps = $store->findCurrentSteps($entryId);
        if (!$currentSteps instanceof SplObjectStorage) {
            $errMsg = 'Invalid currentSteps';
            throw new InternalWorkflowException($errMsg);
        }

        $action = null;

        $ps = $store->getPropertySet($entryId);

        $engineManager = $this->getEngineManager();
        $engineManager->getDataEngine()->populateTransientMap($entry, $transientVars, $wf->getRegisters(), $actionId, $currentSteps, $ps);

        $validGlobalActions = $this->getValidGlobalActions($wf, $actionId, $transientVars, $ps);
        if ($validGlobalActions) {
            $action = $validGlobalActions;
        }
        $validActionsFromCurrentSteps = $this->getValidActionsFromCurrentSteps($currentSteps, $wf, $actionId, $transientVars, $ps);
        if ($validActionsFromCurrentSteps) {
            $action = $validActionsFromCurrentSteps;
        }

        if (null === $action) {
            $errMsg = sprintf(
                'Action %s is invalid',
                $actionId
            );
            throw new InvalidActionException($errMsg);
        }


        try {
            $transitionManager = $this->getEngineManager()->getTransitionEngine();
            if ($transitionManager->transitionWorkflow($entry, $currentSteps, $store, $wf, $action, $transientVars, $inputs, $ps)) {
                $this->checkImplicitFinish($action, $entryId);
            }
        } catch (WorkflowException $e) {
            $this->context->setRollbackOnly();
            /** @var  WorkflowException $e*/
            throw $e;
        }
    }

    /**
     * @param WorkflowDescriptor     $wf
     * @param                        $actionId
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface   $ps
     *
     * @return ActionDescriptor|null
     */
    protected function getValidGlobalActions(WorkflowDescriptor $wf, $actionId, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $resultAction = null;
        $transitionEngine = $this->getEngineManager()->getTransitionEngine();
        foreach ($wf->getGlobalActions() as $actionDesc) {
            if ($actionId === $actionDesc->getId() && $transitionEngine->isActionAvailable($actionDesc, $transientVars, $ps, 0)) {
                $resultAction = $actionDesc;
            }
        }

        return $resultAction;
    }


    /**
     *
     * @param SplObjectStorage       $currentSteps
     * @param WorkflowDescriptor     $wf
     * @param                        $actionId
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface   $ps
     *
     * @return ActionDescriptor|null
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws InternalWorkflowException
     */
    protected function getValidActionsFromCurrentSteps(SplObjectStorage $currentSteps, WorkflowDescriptor $wf, $actionId, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $transitionEngine = $this->getEngineManager()->getTransitionEngine();
        $resultAction = null;
        foreach ($currentSteps as $step) {
            if (!$step instanceof StepInterface) {
                $errMsg = 'Invalid step';
                throw new InternalWorkflowException($errMsg);
            }
            $s = $wf->getStep($step->getStepId());

            foreach ($s->getActions() as $actionDesc) {
                if (!$actionDesc instanceof ActionDescriptor) {
                    $errMsg = 'Invalid action descriptor';
                    throw new InternalWorkflowException($errMsg);
                }

                if ($actionId === $actionDesc->getId() && $transitionEngine->isActionAvailable($actionDesc, $transientVars, $ps, $s->getId())) {
                    $resultAction = $actionDesc;
                }
            }
        }

        return $resultAction;
    }

    /**
     * @param ActionDescriptor $action
     * @param                  $id
     *
     * @return void
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws InvalidArgumentException
     * @throws InternalWorkflowException
     */
    protected function checkImplicitFinish(ActionDescriptor $action, $id)
    {
        $store = $this->getPersistence();
        $entry = $store->findEntry($id);

        $wf = $this->getConfiguration()->getWorkflow($entry->getWorkflowName());

        $currentSteps = $store->findCurrentSteps($id);

        $isCompleted = $wf->getGlobalActions()->count() === 0;

        foreach ($currentSteps as $step) {
            if ($isCompleted) {
                break;
            }

            $stepDes = $wf->getStep($step->getStepId());

            if ($stepDes->getActions()->count() > 0) {
                $isCompleted = true;
            }
        }

        if ($isCompleted) {
            $entryEngine = $this->getEngineManager()->getEntryEngine();
            $entryEngine->completeEntry($action, $id, $currentSteps, WorkflowEntryInterface::COMPLETED);
        }
    }



    /**
     *
     * Check if the state of the specified workflow instance can be changed to the new specified one.
     *
     * @param integer $id The workflow instance id.
     * @param integer $newState The new state id.
     *
     * @return boolean true if the state of the workflow can be modified, false otherwise.
     *
     * @throws InternalWorkflowException
     */
    public function canModifyEntryState($id, $newState)
    {
        $store = $this->getPersistence();
        $entry = $store->findEntry($id);

        $currentState = $entry->getState();

        return array_key_exists($newState, $this->mapEntryState) && array_key_exists($currentState, $this->mapEntryState[$newState]);
    }


    /**
     *
     * Возвращает коллекцию объектов описывающие состояние для текущего экземпляра workflow
     *
     * @param integer $entryId id экземпляра workflow
     *
     * @return SplObjectStorage|StepInterface[]
     *
     * @throws InternalWorkflowException
     */
    public function getCurrentSteps($entryId)
    {
        return $this->getStepFromStorage($entryId, static::CURRENT_STEPS);
    }

    /**
     * Возвращает информацию о том в какие шаги, были осуществленны переходы, для процесса workflow с заданным id
     *
     * @param integer $entryId уникальный идентификатор процесса workflow
     *
     * @return StepInterface[]|SplObjectStorage список шагов
     *
     * @throws InternalWorkflowException
     */
    public function getHistorySteps($entryId)
    {
        return $this->getStepFromStorage($entryId, static::HISTORY_STEPS);
    }

    /**
     * Получение шагов информации о шагах процесса workflow
     *
     * @param $entryId
     * @param $type
     *
     * @return Spi\StepInterface[]|SplObjectStorage
     *
     * @throws InternalWorkflowException
     */
    protected function getStepFromStorage($entryId, $type)
    {
        try {
            $store = $this->getPersistence();

            if (static::CURRENT_STEPS === $type) {
                return $store->findCurrentSteps($entryId);
            } elseif (static::HISTORY_STEPS === $type) {
                return $store->findHistorySteps($entryId);
            }
        } catch (StoreException $e) {
            $errMsg = sprintf(
                'Ошибка при получение истории шагов для экземпляра workflow c id# %s',
                $entryId
            );
            $this->getLog()->error($errMsg, [$e]);
        }

        return new SplObjectStorage();
    }



    /**
     *
     *
     * Modify the state of the specified workflow instance.
     * @param integer $id The workflow instance id.
     * @param integer $newState the new state to change the workflow instance to.
     *
     * @throws InvalidArgumentException
     * @throws InvalidEntryStateException
     * @throws InternalWorkflowException
     */
    public function changeEntryState($id, $newState)
    {
        $store = $this->getPersistence();
        $entry = $store->findEntry($id);

        if ($newState === $entry->getState()) {
            return;
        }

        if ($this->canModifyEntryState($id, $newState)) {
            if (WorkflowEntryInterface::KILLED === $newState || WorkflowEntryInterface::COMPLETED === $newState) {
                $currentSteps = $this->getCurrentSteps($id);

                if (count($currentSteps) > 0) {
                    $entryEngine = $this->getEngineManager()->getEntryEngine();
                    $entryEngine->completeEntry(null, $id, $currentSteps, $newState);
                }
            }

            $store->setEntryState($id, $newState);
        } else {
            $errMsg = sprintf(
                'Не возможен переход в экземпляре workflow #%s. Текущее состояние %s, ожидаемое состояние %s',
                $id,
                $entry->getState(),
                $newState
            );

            throw new InvalidEntryStateException($errMsg);
        }

        $msg = sprintf(
            '%s : Новое состояние: %s',
            $entry->getId(),
            $entry->getState()
        );
        $this->getLog()->debug($msg);
    }





    /**
     * Проверяет имеет ли пользователь достаточно прав, что бы иниициировать вызываемый процесс
     *
     * @param string $workflowName имя workflow
     * @param integer $initialAction id начального состояния
     * @param TransientVarsInterface $inputs
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     * @throws WorkflowException
     * @throws InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function canInitialize($workflowName, $initialAction, TransientVarsInterface $inputs = null)
    {
        $mockWorkflowName = $workflowName;
        $mockEntry = new SimpleWorkflowEntry(0, $mockWorkflowName, WorkflowEntryInterface::CREATED);

        try {
            $ps = PropertySetManager::getInstance('memory', []);
            if (!$ps instanceof PropertySetInterface) {
                $errMsg = 'Invalid create PropertySet';
                throw new InternalWorkflowException($errMsg);
            }
        } catch (\Exception $e) {
            throw new InternalWorkflowException($e->getMessage(), $e->getCode(), $e);
        }




        if (null === $inputs) {
            $inputs = $this->transientVarsFactory();
        }
        $transientVars = $inputs;

        try {
            $this->getEngineManager()->getDataEngine()->populateTransientMap($mockEntry, $transientVars, [], $initialAction, [], $ps);

            $result = $this->canInitializeInternal($workflowName, $initialAction, $transientVars, $ps);

            return $result;
        } catch (InvalidActionException $e) {
            $this->getLog()->error($e->getMessage(), [$e]);

            return false;
        } catch (WorkflowException $e) {
            $errMsg = sprintf(
                'Ошибка при проверки canInitialize: %s',
                $e->getMessage()
            );
            $this->getLog()->error($errMsg, [$e]);

            return false;
        }
    }


    /**
     * Проверяет имеет ли пользователь достаточно прав, что бы иниициировать вызываемый процесс
     *
     * @param string $workflowName имя workflow
     * @param integer $initialAction id начального состояния
     * @param TransientVarsInterface $transientVars
     *
     * @param PropertySetInterface $ps
     *
     * @return bool
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws InvalidActionException
     * @throws InternalWorkflowException
     * @throws WorkflowException
     */
    protected function canInitializeInternal($workflowName, $initialAction, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $wf = $this->getConfiguration()->getWorkflow($workflowName);

        $actionDescriptor = $wf->getInitialAction($initialAction);

        if (null === $actionDescriptor) {
            $errMsg = sprintf(
                'Некорректное инициирующие действие # %s',
                $initialAction
            );
            throw new InvalidActionException($errMsg);
        }

        $restriction = $actionDescriptor->getRestriction();


        $conditions = null;
        if (null !== $restriction) {
            $conditions = $restriction->getConditionsDescriptor();
        }

        $conditionsEngine = $this->getEngineManager()->getConditionsEngine();
        $passesConditions = $conditionsEngine->passesConditionsByDescriptor($conditions, $transientVars, $ps, 0);

        return $passesConditions;
    }

    /**
     * Возвращает резолвер
     *
     * @return TypeResolverInterface
     */
    public function getResolver()
    {
        if (null !== $this->typeResolver) {
            return $this->typeResolver;
        }

        $classResolver = $this->getDefaultTypeResolverClass();
        $r = new ReflectionClass($classResolver);
        $resolver = $r->newInstance();
        $this->typeResolver = $resolver;

        return $this->typeResolver;
    }

    /**
     * Возвращает хранилище состояния workflow
     *
     * @return WorkflowStoreInterface
     *
     * @throws InternalWorkflowException
     */
    protected function getPersistence()
    {
        return $this->getConfiguration()->getWorkflowStore();
    }

    /**
     * Получить конфигурацию workflow. Метод также проверяет была ли иницилазированн конфигурация, если нет, то
     * инициализирует ее.
     *
     * Если конфигурация не была установленна, то возвращает конфигурацию по умолчанию
     *
     * @return ConfigurationInterface|DefaultConfiguration Конфигурация которая была установленна
     *
     * @throws InternalWorkflowException
     */
    public function getConfiguration()
    {
        $config = null !== $this->configuration ? $this->configuration : DefaultConfiguration::getInstance();

        if (!$config->isInitialized()) {
            try {
                $config->load(null);
            } catch (FactoryException $e) {
                $errMsg = 'Ошибка при иницилазации конфигурации workflow';
                $this->getLog()->critical($errMsg, ['exception' => $e]);
                throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
            }
        }

        return $config;
    }

    /**
     * @return LoggerInterface
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param LoggerInterface $log
     *
     * @return $this
     *
     * @throws InternalWorkflowException
     */
    public function setLog($log)
    {
        try {
            LogFactory::validLogger($log);
        } catch (\Exception $e) {
            $errMsg = 'Ошибка при валидации логера';
            throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
        }


        $this->log = $log;

        return $this;
    }


    /**
     * Get the workflow descriptor for the specified workflow name.
     *
     * @param string $workflowName The workflow name.
     * @return WorkflowDescriptor
     *
     * @throws InternalWorkflowException
     */
    public function getWorkflowDescriptor($workflowName)
    {
        try {
            return $this->getConfiguration()->getWorkflow($workflowName);
        } catch (FactoryException $e) {
            $errMsg = 'Ошибка при загрузке workflow';
            $this->getLog()->error($errMsg, ['exception' => $e]);
            throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
        }
    }


    /**
     * Executes a special trigger-function using the context of the given workflow instance id.
     * Note that this method is exposed for Quartz trigger jobs, user code should never call it.
     *
     * @param integer $id The workflow instance id
     * @param integer $triggerId The id of the special trigger-function
     *
     * @throws InvalidArgumentException
     * @throws WorkflowException
     * @throws InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function executeTriggerFunction($id, $triggerId)
    {
        $store = $this->getPersistence();
        $entry = $store->findEntry($id);

        if (null === $entry) {
            $errMsg = sprintf(
                'Ошибка при выполнение тригера # %s для несуществующего экземпляра workflow id# %s',
                $triggerId,
                $id
            );
            $this->getLog()->warning($errMsg);
            return;
        }

        $wf = $this->getConfiguration()->getWorkflow($entry->getWorkflowName());

        $ps = $store->getPropertySet($id);
        $transientVars = $this->transientVarsFactory();

        $this->getEngineManager()->getDataEngine()->populateTransientMap($entry, $transientVars, $wf->getRegisters(), null, $store->findCurrentSteps($id), $ps);

        $functionsEngine = $this->getEngineManager()->getFunctionsEngine();
        $functionsEngine->executeFunction($wf->getTriggerFunction($triggerId), $transientVars, $ps);
    }


    /**
     * @param WorkflowDescriptor   $wf
     * @param StepInterface        $step
     * @param TransientVarsInterface                $transientVars
     * @param PropertySetInterface $ps
     *
     * @return array
     *
     * @throws InternalWorkflowException
     * @throws WorkflowException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    protected function getAvailableActionsForStep(WorkflowDescriptor $wf, StepInterface $step, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $l = [];
        $s = $wf->getStep($step->getStepId());

        if (null === $s) {
            $errMsg = sprintf(
                'getAvailableActionsForStep вызван с не существующим id шага %s',
                $step->getStepId()
            );

            $this->getLog()->warning($errMsg);

            return $l;
        }

        $actions  = $s->getActions();

        if (null === $actions || 0  === $actions->count()) {
            return $l;
        }

        $conditionsEngine = $this->getEngineManager()->getConditionsEngine();
        foreach ($actions as $action) {
            $restriction = $action->getRestriction();
            $conditions = null;

            $transientVars['actionId'] = $action->getId();


            if (null !== $restriction) {
                $conditions = $restriction->getConditionsDescriptor();
            }

            $f = $conditionsEngine->passesConditionsByDescriptor($wf->getGlobalConditions(), $transientVars, $ps, $s->getId())
                 && $conditionsEngine->passesConditionsByDescriptor($conditions, $transientVars, $ps, $s->getId());
            if ($f) {
                $l[] = $action->getId();
            }
        }

        return $l;
    }

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return $this
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Возвращает состояние для текущего экземпляра workflow
     *
     * @param integer $id id экземпляра workflow
     * @return integer id текущего состояния
     *
     * @throws InternalWorkflowException
     */
    public function getEntryState($id)
    {
        try {
            $store = $this->getPersistence();

            return $store->findEntry($id)->getState();
        } catch (StoreException $e) {
            $errMsg = sprintf(
                'Ошибка при получение состояния экземпляра workflow c id# %s',
                $id
            );
            $this->getLog()->error($errMsg, [$e]);
        }

        return WorkflowEntryInterface::UNKNOWN;
    }


    /**
     * Настройки хранилища
     *
     * @return array
     *
     * @throws InternalWorkflowException
     */
    public function getPersistenceProperties()
    {
        return $this->getConfiguration()->getPersistenceArgs();
    }


    /**
     * Get the PropertySet for the specified workflow instance id.
     * @param integer $id The workflow instance id.
     *
     * @return PropertySetInterface
     * @throws InternalWorkflowException
     */
    public function getPropertySet($id)
    {
        $ps = null;

        try {
            $ps = $this->getPersistence()->getPropertySet($id);
        } catch (StoreException $e) {
            $errMsg = sprintf(
                'Ошибка при получение PropertySet для экземпляра workflow c id# %s',
                $id
            );
            $this->getLog()->error($errMsg, [$e]);
        }

        return $ps;
    }

    /**
     * @return string[]
     *
     * @throws InternalWorkflowException
     */
    public function getWorkflowNames()
    {
        try {
            return $this->getConfiguration()->getWorkflowNames();
        } catch (FactoryException $e) {
            $errMsg = 'Ошибка при получение имен workflow';
            $this->getLog()->error($errMsg, [$e]);
        }

        return [];
    }

    /**
     * @param TypeResolverInterface $typeResolver
     *
     * @return $this
     */
    public function setTypeResolver(TypeResolverInterface $typeResolver)
    {
        $this->typeResolver = $typeResolver;

        return $this;
    }


    /**
     * Get a collection (Strings) of currently defined permissions for the specified workflow instance.
     * @param integer $id id the workflow instance id.
     * @param TransientVarsInterface $inputs inputs The inputs to the workflow instance.
     *
     * @return array  A List of permissions specified currently (a permission is a string name).
     *
     */
    public function getSecurityPermissions($id, TransientVarsInterface $inputs = null)
    {
        try {
            $store = $this->getPersistence();
            $entry = $store->findEntry($id);
            $wf = $this->getConfiguration()->getWorkflow($entry->getWorkflowName());

            $ps = $store->getPropertySet($id);

            if (null === $inputs) {
                $inputs = $this->transientVarsFactory();
            }
            $transientVars = $inputs;

            $currentSteps = $store->findCurrentSteps($id);

            $engineManager = $this->getEngineManager();
            try {
                $engineManager->getDataEngine()->populateTransientMap($entry, $transientVars, $wf->getRegisters(), null, $currentSteps, $ps);
            } catch (\Exception $e) {
                $errMsg = sprintf(
                    'Внутреннея ошибка: %s',
                    $e->getMessage()
                );
                throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
            }


            $s = [];

            $conditionsEngine = $engineManager->getConditionsEngine();
            foreach ($currentSteps as $step) {
                $stepId = $step->getStepId();

                $xmlStep = $wf->getStep($stepId);

                $securities = $xmlStep->getPermissions();

                foreach ($securities as $security) {
                    if (!$security instanceof PermissionDescriptor) {
                        $errMsg = 'Invalid PermissionDescriptor';
                        throw new InternalWorkflowException($errMsg);
                    }
                    $conditionsDescriptor = $security->getRestriction()->getConditionsDescriptor();
                    if (null !== $security->getRestriction() && $conditionsEngine->passesConditionsByDescriptor($conditionsDescriptor, $transientVars, $ps, $xmlStep->getId())) {
                        $s[$security->getName()] = $security->getName();
                    }
                }
            }

            return $s;
        } catch (\Exception $e) {
            $errMsg = sprintf(
                'Ошибка при получение информации о правах доступа для экземпляра workflow c id# %s',
                $id
            );
            $this->getLog()->error($errMsg, [$e]);
        }

        return [];
    }


    /**
     * Get the name of the specified workflow instance.
     *
     * @param integer $id the workflow instance id.
     *
     * @return string
     *
     * @throws InternalWorkflowException
     */
    public function getWorkflowName($id)
    {
        try {
            $store = $this->getPersistence();
            $entry = $store->findEntry($id);

            if (null !== $entry) {
                return $entry->getWorkflowName();
            }
        } catch (FactoryException $e) {
            $errMsg = sprintf(
                'Ошибка при получение имен workflow для инстанса с id # %s',
                $id
            );
            $this->getLog()->error($errMsg, [$e]);
        }

        return null;
    }

    /**
     * Удаляет workflow
     *
     * @param string $workflowName
     *
     * @return bool
     *
     * @throws InternalWorkflowException
     */
    public function removeWorkflowDescriptor($workflowName)
    {
        return $this->getConfiguration()->removeWorkflow($workflowName);
    }

    /**
     * @param                    $workflowName
     * @param WorkflowDescriptor $descriptor
     * @param                    $replace
     *
     * @return bool
     *
     * @throws InternalWorkflowException
     */
    public function saveWorkflowDescriptor($workflowName, WorkflowDescriptor $descriptor, $replace)
    {
        $success = $this->getConfiguration()->saveWorkflow($workflowName, $descriptor, $replace);

        return $success;
    }


    /**
     * Query the workflow store for matching instances
     *
     * @param WorkflowExpressionQuery $query
     *
     * @return array
     *
     * @throws InternalWorkflowException
     */
    public function query(WorkflowExpressionQuery $query)
    {
        return $this->getPersistence()->query($query);
    }

    /**
     * @return string
     */
    public function getDefaultTypeResolverClass()
    {
        return $this->defaultTypeResolverClass;
    }

    /**
     * @param string $defaultTypeResolverClass
     *
     * @return $this
     */
    public function setDefaultTypeResolverClass($defaultTypeResolverClass)
    {
        $this->defaultTypeResolverClass = (string)$defaultTypeResolverClass;

        return $this;
    }


    /**
     * @return WorkflowContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }
}
