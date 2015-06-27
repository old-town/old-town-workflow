<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Log\LogFactory;
use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Config\ConfigurationInterface;
use OldTown\Workflow\Config\DefaultConfiguration;
use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\InvalidActionException;
use OldTown\Workflow\Exception\InvalidArgumentException;
use OldTown\Workflow\Exception\InvalidInputException;
use OldTown\Workflow\Exception\InvalidRoleException;
use OldTown\Workflow\Exception\StoreException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Loader\ConditionDescriptor;
use OldTown\Workflow\Loader\ConditionsDescriptor;
use OldTown\Workflow\Loader\RegisterDescriptor;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use Psr\Log\LoggerInterface;
use Traversable;
use SplObjectStorage;



/**
 * Class AbstractWorkflow
 *
 * @package OldTown\Workflow
 */
abstract class  AbstractWorkflow implements WorkflowInterface
{
    /**
     * @var WorkflowContextInterface
     */
    protected $context;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     *
     * @var array
     */
    private $stateCache = [];

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * Логер
     *
     * @var LoggerInterface
     */
    protected $log;

    /**
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     */
    public function __construct()
    {
        try {
            $this->log = LogFactory::getLog();
        } catch (\Exception $e) {
            $errMsg = 'Ошибка при инициализации подсистемы логирования';
            throw new InternalWorkflowException($errMsg);
        }

    }


    /**
     * Инициализация workflow. Workflow нужно иницаилизровать прежде, чем выполнять какие либо действия.
     * Workflow может быть инициализированно только один раз
     *
     * @param string $workflowName Имя workflow
     * @param integer $initialAction Имя первого шага, с которого начинается workflow
     * @param array $inputs Данные введеные пользователем
     * @return integer
     * @throws \OldTown\Workflow\Exception\InvalidRoleException
     * @throws \OldTown\Workflow\Exception\InvalidInputException
     * @throws \OldTown\Workflow\Exception\WorkflowException
     * @throws \OldTown\Workflow\Exception\InvalidEntryStateException
     * @throws \OldTown\Workflow\Exception\InvalidActionException
     * @throws \OldTown\Workflow\Exception\FactoryException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\StoreException
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     *
     */
    public function initialize($workflowName, $initialAction, array $inputs = null)
    {
        $initialAction = (integer)$initialAction;

        $wf = $this->getConfiguration()->getWorkflow($workflowName);

        $store = $this->getPersistence();

        $entry = $store->createEntry($workflowName);

        $ps = $store->getPropertySet($entry->getId());


        $transientVars = [];

        if (null !== $inputs) {
            $transientVars = $inputs;
        }


        $this->populateTransientMap($entry, $transientVars, $wf->getRegisters(), $initialAction, [], $ps);

        if (!$this->canInitializeInternal($workflowName, $initialAction, $transientVars, $ps)) {
            $this->context->setRollbackOnly();
            $errMsg = 'Вы не можете инициироват данный рабочий процесс';
            throw new InvalidRoleException($errMsg);
        }
    }

    /**
     * Проверяет имеет ли пользователь достаточно прав, что бы иниициировать вызываемый процесс
     *
     * @param string     $workflowName  имя workflow
     * @param integer    $initialAction id начального состояния
     * @param array|null $inputs
     *
     * @return bool
     */
    public function canInitialize($workflowName, $initialAction, array $inputs = null)
    {
        // TODO: Implement canInitialize() method.
    }


    /**
     * Проверяет имеет ли пользователь достаточно прав, что бы иниициировать вызываемый процесс
     *
     * @param string               $workflowName  имя workflow
     * @param integer              $initialAction id начального состояния
     * @param array|null           $transientVars
     *
     * @param PropertySetInterface $ps
     *
     * @return bool
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws \OldTown\Workflow\Exception\InvalidActionException
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\WorkflowException
     */
    protected function canInitializeInternal($workflowName, $initialAction, array $transientVars = null, PropertySetInterface $ps)
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

        $passesConditions = $this->passesConditionsByDescriptor($conditions, $transientVars, $ps, 0);

        return $passesConditions;
    }

    /**
     * @param ConditionsDescriptor $descriptor
     * @param array                $transientVars
     * @param PropertySetInterface $ps
     * @param                      $currentStepId
     *
     * @return bool
     *
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\WorkflowException
     */
    protected function passesConditionsByDescriptor(ConditionsDescriptor $descriptor = null, array $transientVars = [], PropertySetInterface $ps, $currentStepId)
    {
        if (null === $descriptor) {
            return true;
        }

        $type = $descriptor->getType();
        $conditions = $descriptor->getConditions();
        $passesConditions = $this->passesConditions($type, $conditions, $transientVars, $ps, $currentStepId);

        return $passesConditions;
    }

    /**
     * @param string               $conditionType
     * @param array                $conditionsStorage
     * @param array                $transientVars
     * @param PropertySetInterface $ps
     * @param integer              $currentStepId
     *
     * @return bool
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\WorkflowException
     */
    protected function passesConditions($conditionType, $conditionsStorage = null, array $transientVars = [], PropertySetInterface $ps, $currentStepId)
    {
        if (null === $conditionsStorage) {
            return true;
        }


        if ($conditionsStorage instanceof Traversable) {
            $conditions = [];
            foreach ($conditionsStorage as $k => $v) {
                $conditions[$k] = $v;
            }
        } elseif (is_array($conditionsStorage)) {
            $conditions = $conditionsStorage;
        } else {
            $errMsg = 'Conditions должен быть массивом, либо реализовывать интерфейс Traversable';
            throw new InvalidArgumentException($errMsg);
        }


        if (0 === count($conditions)) {
            return true;
        }


        $and = strtoupper($conditionType) === 'AND';
        $or = !$and;

        foreach ($conditions as $descriptor) {
            if ($descriptor instanceof ConditionsDescriptor) {
                $result = $this->passesConditions($descriptor->getType(), $descriptor->getConditions(), $transientVars, $ps, $currentStepId);
            } else {
                $result = $this->passesCondition($descriptor, $transientVars, $ps, $currentStepId);
            }

            if ($and && !$result) {
                return false;
            } elseif ($or && $result) {
                return true;
            }
        }

        if ($and) {
            return true;
        } elseif ($or) {
            return false;
        } else {
            return false;
        }
    }

    /**
     * @param ConditionDescriptor  $conditionDesc
     * @param array                $transientVars
     * @param PropertySetInterface $ps
     * @param integer              $currentStepId
     *
     * @return boolean
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\WorkflowException
     */
    protected function passesCondition(ConditionDescriptor $conditionDesc, array $transientVars = [], PropertySetInterface $ps, $currentStepId)
    {
        $type = $conditionDesc->getType();

        $argsOriginal = $conditionDesc->getArgs();


        $args = [];
        foreach ($argsOriginal as $key => $value) {
            $translateValue = $this->getConfiguration()->getVariableResolver()->translateVariables($value, $transientVars, $ps);
            $args[$key] = $translateValue;
        }

        if (-1 !== $currentStepId) {
            $stepId = array_key_exists('stepId', $args) ? (integer)$args['stepId'] : null;

            if (null !== $stepId && -1 === $stepId) {
                $args['stepId'] = $currentStepId;
            }
        }

        $condition = $this->getResolver()->getCondition($type, $args);

        if (null === $condition) {
            $this->context->setRollbackOnly();
            $errMsg = 'Огибка при загрузки условия';
            throw new WorkflowException($errMsg);
        }

        try {
            $passed = $condition->passesCondition($transientVars, $args, $ps);

            if ($conditionDesc->isNegate()) {
                $passed = !$passed;
            }
        } catch (\Exception $e) {
            $this->context->setRollbackOnly();

            $errMsg = sprintf(
                'Ошбика при выполнение условия %s',
                get_class($condition)
            );

            throw new WorkflowException($errMsg, $e->getCode(), $e);
        }

        return $passed;
    }

    /**
     * @param WorkflowEntryInterface $entry
     * @param array $transientVars
     * @param array|Traversable|RegisterDescriptor[]|SplObjectStorage $registersStorage
     * @param integer $actionId
     * @param array $currentSteps
     * @param PropertySetInterface $ps
     *
     *
     * @return $this
     *
     * @throws \OldTown\Workflow\Exception\WorkflowException
     * @throws \OldTown\Workflow\Exception\FactoryException
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\StoreException
     */
    protected function populateTransientMap(WorkflowEntryInterface $entry, array &$transientVars, $registersStorage, $actionId = null, array $currentSteps, PropertySetInterface $ps)
    {
        if ($registersStorage instanceof Traversable) {
            $registers = [];
            foreach ($registersStorage as $k => $v) {
                $registers[$k] = $v;
            }
        } elseif (is_array($registersStorage)) {
            $registers = $registersStorage;
        } else {
            $errMsg = 'Registers должен быть массивом, либо реализовывать интерфейс Traversable';
            throw new InvalidArgumentException($errMsg);
        }
        /** @var RegisterDescriptor[]  $registers*/

        $transientVars['context'] = $this->context;
        $transientVars['entry'] = $entry;
        $transientVars['store'] = $this->getPersistence();
        $transientVars['configuration'] = $this->getConfiguration();
        $transientVars['descriptor'] = $this->getConfiguration()->getWorkflow($entry->getWorkflowName());

        if (null !== $actionId) {
            $actionId = (integer)$actionId;
            $transientVars['actionId'] = $actionId;
        }

        $transientVars['currentSteps'] = $currentSteps;


        foreach ($registers as $register) {
            $args = $register->getArgs();
            $type = $register->getType();


            $r = $this->getResolver()->getRegister($type, $args);

            if (null === $r) {
                $errMsg = 'Ошибка при инициализации register';
                $this->context->setRollbackOnly();
                throw new WorkflowException($errMsg);
            }

            try {
                $variableName = $register->getVariableName();
                $value = $r->registerVariable($this->context, $entry, $args, $ps);

                $transientVars[$variableName] = $value;
            } catch (\Exception $e) {
                $this->context->setRollbackOnly();

                $errMsg = sprintf(
                    'Ошибка при регистрации переменной в регистре %s',
                    get_class($r)
                );

                throw new WorkflowException($errMsg, $e->getCode(), $e);
            }
        }
    }

    /**
     * Возвращает резолвер
     *
     * @return TypeResolver
     */
    public function getResolver()
    {
        if (null !== $this->typeResolver) {
            return $this->typeResolver;
        }

        $this->typeResolver = TypeResolver::getResolver();

        return $this->typeResolver;
    }
    /**
     * Возвращает хранилище состояния workflow
     *
     * @return WorkflowStoreInterface
     * @throws StoreException
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
     * @throws InternalWorkflowException
     */
    public function getWorkflowDescriptor($workflowName)
    {
        try {
            $w = $this->getConfiguration()->getWorkflow($workflowName);
            return $w;
        } catch (FactoryException $e) {
            $errMsg = 'Ошибка при загрузке workflow';
            $this->getLog()->error($errMsg, ['exception' => $e]);
            throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
        }
    }
########################################################################################################################
#Методы заглушки, при портирование заменять на реализацию ##############################################################
########################################################################################################################


    /**
     * Возвращает коллекцию объектов описывающие состояние для текущего экземпляра workflow
     *
     * @param integer $id id экземпляра workflow
     * @return array
     */
    public function getCurrentSteps($id)
    {
    }

    /**
     * Возвращает состояние для текущего экземпляра workflow
     *
     * @param integer $id id экземпляра workflow
     * @return integer id текущего состояния
     */
    public function getEntryState($id)
    {
    }

    /**
     * Returns a list of all steps that are completed for the given workflow instance id.
     *
     * @param integer $id The workflow instance id.
     * @return StepInterface[] a List of Steps
     */
    public function getHistorySteps($id)
    {
    }

    /**
     * Get the PropertySet for the specified workflow instance id.
     * @param integer $id The workflow instance id.
     * @return PropertySetInterface
     */
    public function getPropertySet($id)
    {
    }

    /**
     * Get a collection (Strings) of currently defined permissions for the specified workflow instance.
     * @param integer $id id the workflow instance id.
     * @param array $inputs inputs The inputs to the workflow instance.
     * @return array  A List of permissions specified currently (a permission is a string name).
     */
    public function getSecurityPermissions($id, array $inputs = [])
    {
    }


    /**
     * Get the name of the specified workflow instance.
     *
     * @param integer $id the workflow instance id.
     * @return string
     */
    public function getWorkflowName($id)
    {
    }



    /**
     * Check if the state of the specified workflow instance can be changed to the new specified one.
     * @param integer $id The workflow instance id.
     * @param integer $newState The new state id.
     * @return boolean true if the state of the workflow can be modified, false otherwise.
     */
    public function canModifyEntryState($id, $newState)
    {
    }

    /**
     * Modify the state of the specified workflow instance.
     * @param integer $id The workflow instance id.
     * @param integer $newState the new state to change the workflow instance to.
     * @throws WorkflowException
     * If the new state is {@link com.opensymphony.workflow.spi.WorkflowEntry.KILLED}
     * or {@link com.opensymphony.workflow.spi.WorkflowEntry.COMPLETED}
     * then all current steps are moved to history steps. If the new state is
     */
    public function changeEntryState($id, $newState)
    {
    }

    /**
     * Perform an action on the specified workflow instance.
     * @param integer $id The workflow instance id.
     * @param integer $actionId The action id to perform (action id's are listed in the workflow descriptor).
     * @param array $inputs The inputs to the workflow instance.
     * @throws InvalidInputException if a validator is specified and an input is invalid.
     * @throws WorkflowException if the action is invalid for the specified workflow
     * instance's current state.
     */
    public function doAction($id, $actionId, array $inputs = [])
    {
    }

    /**
     * Executes a special trigger-function using the context of the given workflow instance id.
     * Note that this method is exposed for Quartz trigger jobs, user code should never call it.
     * @param integer $id The workflow instance id
     * @param integer $triggerId The id of the special trigger-function
     * @thrown WorkflowException
     */
    public function executeTriggerFunction($id, $triggerId)
    {
    }

    /**
     * Query the workflow store for matching instances
     *
     * @param WorkflowExpressionQuery $query
     * @throws WorkflowException
     * @return array
     */
    public function query(WorkflowExpressionQuery $query)
    {
    }
}
