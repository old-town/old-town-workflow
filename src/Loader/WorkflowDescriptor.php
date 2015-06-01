<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class WorkflowDescriptor
{
    /**
     * Имя workflow
     *
     * @var string|null
     */
    protected $workflowName;

    /**
     * Возвращает имя workflow
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->workflowName;
    }

    /**
     * Устанавливает имя workflow
     *
     * @param string $workflowName
     *
     * @return $this
     */
    public function setName($workflowName)
    {
        $this->workflowName = (string)$workflowName;

        return $this;
    }

}
