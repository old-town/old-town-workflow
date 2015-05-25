<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;


/**
 * Interface WorkflowFactoryInterface
 *
 * @package OldTown\Workflow\Util
 */
interface  WorkflowFactoryInterface
{
    /**
     * @param string $workflowName
     * @param Object $layout
     *
     * @return mixed
     */
public function setLayout($workflowName, $layout);

    /**
     * @param string $workflowName
     *
     * @return Object
     */
    public function getLayout($workflowName);

    /**
     * @param String $name
     *
     * @return boolean
     */
    public function isModifiable($name);

    /**
     * @return string
     */
    public function getName();

    /**
     *
     * @return []
     */
    public function getProperties();

    /**
     * @param String $name
     * @param boolean $validate
     * @return WorkflowDescriptor
     * @throws FactoryException
     */
    public function getWorkflow($name, $validate = false);

    /**
     * Возвращает имена всех workflow, которые может создать данная фабрика
     * @return String[]
     * @throws FactoryException
     */
    public function getWorkflowNames();

    /**
     * @param $name
     *
     * @return void
     */
    public function createWorkflow($name);

    /**
     * @param array $p
     *
     * @return void
     */
    public function init(array $p = []);

    /**
     * @return void
     * @throws FactoryException
     */
    public function initDone();

    /**
     * @param string $name
     * @return boolean
     * @throws FactoryException
     */
    public function removeWorkflow($name);

    /**
     * @param string $name
     * @return void
     */
    public function save($name);

    /**
     * Save the workflow.
     * Is it the responsibility of the caller to ensure that the workflow is valid,
     * through the {@link WorkflowDescriptor#validate()} method. Invalid workflows will
     * be saved without being checked.
     * @param string $name имя workflow
     * @param WorkflowDescriptor $descriptor Дескриптор для  workflow.
     * @param boolean $replace флаг, определяющий можно ли перезаписать существующее workflow
     * @return boolean
     * @throws FactoryException
     * @throws InvalidWorkflowDescriptorException
     */
    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace);
}
