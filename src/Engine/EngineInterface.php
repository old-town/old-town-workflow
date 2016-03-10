<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\Workflow\WorkflowInterface;


/**
 * Interface EngineInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface EngineInterface
{
    /**
     * Конструктор абстрактного движка
     *
     * AbstractEngine constructor.
     *
     * @param WorkflowInterface $wf
     */
    public function __construct(WorkflowInterface $wf);

    /**
     * Устанавливает менеджер workflow
     *
     * @return WorkflowInterface
     */
    public function getWorkflowManager();

    /**
     * Возвращает менеджер workflow
     *
     * @param WorkflowInterface $workflowManager
     *
     * @return $this
     */
    public function setWorkflowManager(WorkflowInterface $workflowManager);
}
