<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Util\Properties\PropertiesInterface;

/**
 * Interface WorkflowFactoryInterface
 *
 * @package OldTown\Workflow\Loader
 */
interface  WorkflowFactoryInterface
{
    /**
     * @param string $workflowName
     * @param mixed $layout
     * @return $this
     */
    public function setLayout($workflowName, $layout);

    /**
     * @param string $workflowName
     * @return mixed
     */
    public function getLayout($workflowName);


    /**
     * @param string $name
     * @return boolean
     */
    public function isModifiable($name);

    /**
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @param bool $validate
     *
     * @return WorkflowDescriptor
     */
    public function getWorkflow($name, $validate = true);

    /**
     *
     * @return String[]
     */
    public function getWorkflowNames();

    /**
     * @param string $name
     *
     * @return void
     */
    public function createWorkflow($name);

    /**
     *
     * @return void
     */
    public function initDone();

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function removeWorkflow($name);

    /**
     * @param string $oldName
     * @param string $newName
     * @return void
     */
    public function renameWorkflow($newName, $oldName = null);

    /**
     * @return void
     */
    public function save();


    /**
     * Сохраняет workflow
     *
     * @param string $name имя workflow
     * @param WorkflowDescriptor $descriptor descriptor workflow
     * @param boolean $replace если true - то в случае существования одноименного workflow, оно будет заменено
     * @return boolean true - если workflow было сохранено
     */
    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace);

    /**
     * @return PropertiesInterface
     */
    public function getProperties();

    /**
     * @param PropertiesInterface $p
     *
     * @return void
     */
    public function init(PropertiesInterface $p);
}
