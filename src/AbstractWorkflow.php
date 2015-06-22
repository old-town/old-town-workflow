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
use OldTown\Workflow\Exception\InvalidEntryStateException;
use OldTown\Workflow\Exception\InvalidInputException;
use OldTown\Workflow\Exception\InvalidRoleException;
use OldTown\Workflow\Exception\StoreException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use Psr\Log\LoggerInterface;

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
     */
    public function __construct()
    {
        $this->log = LogFactory::getLog();
    }


    /**
     * Инициализация workflow. Workflow нужно иницаилизровать прежде, чем выполнять какие либо действия.
     * Workflow может быть инициализированно только один раз
     *
     * @param string $workflowName Имя workflow
     * @param integer $initialAction Имя первого шага, с которого начинается workflow
     * @param array $inputs Данные введеные пользователем
     * @return integer
     * @throws InvalidRoleException
     * @throws InvalidInputException
     * @throws WorkflowException
     * @throws InvalidEntryStateException
     * @throws InvalidActionException
     * @throws \OldTown\Workflow\Exception\FactoryException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     */
    public function initialize($workflowName, $initialAction, array $inputs = null)
    {
        $wf = $this->getConfiguration()->getWorkflow($workflowName);
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
     * @return ConfigurationInterface Конфигурация которая была установленна
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
     * Check if the calling user has enough permissions to initialise the specified workflow.
     * @param string $workflowName The name of the workflow to check.
     * @param integer $initialStep The id of the initial state to check.
     * @return Boolean true if the user can successfully call initialize, false otherwise.
     */
    public function canInitialize($workflowName, $initialStep)
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
