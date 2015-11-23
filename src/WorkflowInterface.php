<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Workflow\Config\ConfigurationInterface;
use OldTown\Workflow\Config\DefaultConfiguration;
use OldTown\Workflow\Exception\InvalidActionException;
use OldTown\Workflow\Exception\InvalidEntryStateException;
use OldTown\Workflow\Exception\InvalidInputException;
use OldTown\Workflow\Exception\InvalidRoleException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;

/**
 * Interface WorkflowInterface
 *
 * @package OldTown\Workflow
 */
interface WorkflowInterface
{
    /**
     *
     * @var string
     */
    const BSF_COL = 'col';

    /**
     *
     * @var string
     */
    const BSF_LANGUAGE = 'language';

    /**
     *
     * @var string
     */
    const BSF_ROW = 'row';

    /**
     *
     * @var string
     */
    const BSF_SCRIPT = 'script';

    /**
     *
     * @var string
     */
    const BSF_SOURCE = 'source';

    /**
     *
     * @var string
     */
    const BSH_SCRIPT = 'script';


    /**
     *
     * @var string
     */
    const CLASS_NAME = 'class.name';

    /**
     *
     * @var string
     */
    const EJB_LOCATION = 'ejb.location';

    /**
     *
     * @var string
     */
    const JNDI_LOCATION = 'jndi.location';

    /**
     * Возвращает коллекцию объектов описывающие состояние для текущего экземпляра workflow
     *
     * @param integer $id id экземпляра workflow
     * @return []
     */
    public function getCurrentSteps($id);

    /**
     * Возвращает состояние для текущего экземпляра workflow
     *
     * @param integer $id id экземпляра workflow
     * @return integer id текущего состояния
     */
    public function getEntryState($id);

    /**
     * Returns a list of all steps that are completed for the given workflow instance id.
     *
     * @param integer $id The workflow instance id.
     * @return StepInterface[] a List of Steps
     */
    public function getHistorySteps($id);

    /**
     * Get the PropertySet for the specified workflow instance id.
     * @param integer $id The workflow instance id.
     * @return PropertySetInterface
     */
    public function getPropertySet($id);

    /**
     * Get a collection (Strings) of currently defined permissions for the specified workflow instance.
     * @param integer $id id the workflow instance id.
     * @param array $inputs inputs The inputs to the workflow instance.
     * @return [] A List of permissions specified currently (a permission is a string name).
     */
    public function getSecurityPermissions($id, TransientVarsInterface $inputs = null);

    /**
     * Get the workflow descriptor for the specified workflow name.
     *
     * @param string $workflowName The workflow name.
     * @return WorkflowDescriptor
     */
    public function getWorkflowDescriptor($workflowName);

    /**
     * Get the name of the specified workflow instance.
     *
     * @param integer $id the workflow instance id.
     * @return string
     */
    public function getWorkflowName($id);

    /**
     * Проверяет имеет ли пользователь достаточно прав, что бы иниициировать вызываемый процесс
     *
     * @param string  $workflowName  имя workflow
     * @param integer $initialAction id начального состояния
     * @param TransientVarsInterface|null   $inputs
     *
     * @return bool
     */
    public function canInitialize($workflowName, $initialAction, TransientVarsInterface $inputs = null);

    /**
     * Check if the state of the specified workflow instance can be changed to the new specified one.
     * @param integer $id The workflow instance id.
     * @param integer $newState The new state id.
     * @return boolean true if the state of the workflow can be modified, false otherwise.
     */
    public function canModifyEntryState($id, $newState);

    /**
     * Modify the state of the specified workflow instance.
     * @param integer $id The workflow instance id.
     * @param integer $newState the new state to change the workflow instance to.
     * @throws WorkflowException
     * If the new state is {@link com.opensymphony.workflow.spi.WorkflowEntry.KILLED}
     * or {@link com.opensymphony.workflow.spi.WorkflowEntry.COMPLETED}
     * then all current steps are moved to history steps. If the new state is
     */
    public function changeEntryState($id, $newState);

    /**
     * Perform an action on the specified workflow instance.
     * @param integer $id The workflow instance id.
     * @param integer $actionId The action id to perform (action id's are listed in the workflow descriptor).
     * @param TransientVarsInterface $inputs The inputs to the workflow instance.
     * @throws InvalidInputException if a validator is specified and an input is invalid.
     * @throws WorkflowException if the action is invalid for the specified workflow
     * instance's current state.
     */
    public function doAction($id, $actionId, TransientVarsInterface $inputs = null);

    /**
     * Executes a special trigger-function using the context of the given workflow instance id.
     * Note that this method is exposed for Quartz trigger jobs, user code should never call it.
     * @param integer $id The workflow instance id
     * @param integer $triggerId The id of the special trigger-function
     * @thrown WorkflowException
     */
    public function executeTriggerFunction($id, $triggerId);

    /**
     * Инициализация workflow. Workflow нужно иницаилизровать прежде, чем выполнять какие либо действия.
     * Workflow может быть инициализированно только один раз
     *
     * @param string $workflowName Имя workflow
     * @param integer $initialAction Имя первого шага, с которого начинается workflow
     * @param array TransientVarsInterface Данные введеные пользователем
     * @return integer
     * @throws InvalidRoleException
     * @throws InvalidInputException
     * @throws WorkflowException
     * @throws InvalidEntryStateException
     * @throws InvalidActionException
     */
    public function initialize($workflowName, $initialAction, TransientVarsInterface $inputs = null);


    /**
     * Query the workflow store for matching instances
     *
     * @param WorkflowExpressionQuery $query
     * @throws WorkflowException
     * @return array
     */
    public function query(WorkflowExpressionQuery $query);


    /**
     * Получить конфигурацию workflow. Метод также проверяет была ли иницилазированн конфигурация, если нет, то
     * инициализирует ее.
     *
     * Если конфигурация не была установленна, то возвращает конфигурацию по умолчанию
     *
     * @return ConfigurationInterface|DefaultConfiguration Конфигурация которая была установленна
     *
     */
    public function getConfiguration();

    /**
     * Устанавливает конфигурацию workflow
     *
     * @param ConfigurationInterface $configuration
     *
     * @return $this
     */
    public function setConfiguration(ConfigurationInterface $configuration);
}
