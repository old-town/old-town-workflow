<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;

use DOMNode;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class WorkflowDescriptor extends AbstractDescriptor
{
    /**
     * Имя workflow
     *
     * @var string|null
     */
    protected $workflowName;

    /**
     * @param $root
     */
    public function __construct(DOMNode $root)
    {
        $this->init($root);
    }

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

    /**
     * Валидация workflow
     *
     * @throws InvalidWorkflowDescriptorException
     * @return void
     */
    public function validate()
    {

    }

    /**
     * @param DOMNode $root
     */
    protected function init(DOMNode $root)
    {
        die(get_class($root));
    }
}
