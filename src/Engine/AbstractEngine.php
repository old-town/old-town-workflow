<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\Workflow\WorkflowInterface;


/**
 * Class Conditions
 *
 * @package OldTown\Workflow\Engine
 */
class AbstractEngine implements EngineInterface
{
    use WorkflowManagerTrait;

    /**
     * Конструктор абстрактного движка
     *
     * AbstractEngine constructor.
     *
     * @param WorkflowInterface $wf
     */
    public function __construct(WorkflowInterface $wf)
    {
        $this->setWorkflowManager($wf);
    }
}
