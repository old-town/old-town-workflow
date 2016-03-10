<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Workflow\Config\ConfigurationInterface;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use SplObjectStorage;

/**
 * Interface WorkflowInterface
 *
 * @package OldTown\Workflow
 */
interface WorkflowInterface
{
    /**
     * Возвращает коллекцию объектов описывающие состояние для текущего экземпляра workflow
     *
     * @param integer $entryId id экземпляра workflow
     * @return SplObjectStorage|StepInterface[]
     */
    public function getCurrentSteps($entryId);

    /**
     * Возвращает состояние для текущего экземпляра workflow
     *
     * @param integer $id id экземпляра workflow
     * @return integer id текущего состояния
     */
    public function getEntryState($id);

    /**
     * Возвращает информацию о том в какие шаги, были осуществленны переходы, для процесса workflow с заданным id
     *
     * @param integer $entryId уникальный идентификатор процесса workflow
     * @return StepInterface[]|SplObjectStorage a List of Steps
     */
    public function getHistorySteps($entryId);

    /**
     * Get the PropertySet for the specified workflow instance id.
     * @param integer $id The workflow instance id.
     * @return PropertySetInterface
     */
    public function getPropertySet($id);

    /**
     * Get a collection (Strings) of currently defined permissions for the specified workflow instance.
     * @param integer $id id the workflow instance id.
     * @param TransientVarsInterface $inputs inputs The inputs to the workflow instance.
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
     * If the new state is {@link com.opensymphony.workflow.spi.WorkflowEntry.KILLED}
     * or {@link com.opensymphony.workflow.spi.WorkflowEntry.COMPLETED}
     * then all current steps are moved to history steps. If the new state is
     */
    public function changeEntryState($id, $newState);

    /**
     * Осуществляет переходл в новое состояние, для заданного процесса workflow
     *
     * @param integer $entryId id запущенного процесса workflow
     * @param integer $actionId id действия, доступного та текущем шаеге процессса workflow
     * @param TransientVarsInterface $inputs Входные данные для перехода
     */
    public function doAction($entryId, $actionId, TransientVarsInterface $inputs = null);

    /**
     * Executes a special trigger-function using the context of the given workflow instance id.
     * Note that this method is exposed for Quartz trigger jobs, user code should never call it.
     * @param integer $id The workflow instance id
     * @param integer $triggerId The id of the special trigger-function
     *
     */
    public function executeTriggerFunction($id, $triggerId);

    /**
     * Инициализация workflow. Workflow нужно иницаилизровать прежде, чем выполнять какие либо действия.
     * Workflow может быть инициализированно только один раз
     *
     * @param string $workflowName Имя workflow
     * @param integer $initialAction Имя первого шага, с которого начинается workflow
     * @param  TransientVarsInterface $inputs Данные введеные пользователем
     * @return integer
     */
    public function initialize($workflowName, $initialAction, TransientVarsInterface $inputs = null);


    /**
     * Query the workflow store for matching instances
     *
     * @param WorkflowExpressionQuery $query
     * @return array
     */
    public function query(WorkflowExpressionQuery $query);


    /**
     * Получить конфигурацию workflow. Метод также проверяет была ли иницилазированн конфигурация, если нет, то
     * инициализирует ее.
     *
     * Если конфигурация не была установленна, то возвращает конфигурацию по умолчанию
     *
     * @return ConfigurationInterface Конфигурация которая была установленна
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

    /**
     * Устанавливает резолвер
     *
     * @param TypeResolverInterface $typeResolver
     *
     * @return $this
     */
    public function setTypeResolver(TypeResolverInterface $typeResolver);

    /**
     * Возвращает резолвер
     *
     * @return TypeResolverInterface
     */
    public function getResolver();
}
