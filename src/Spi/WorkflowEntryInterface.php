<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Spi;

/**
 * Interface WorkflowEntryInterface
 *
 * @package OldTown\Workflow\Spi
 */
interface WorkflowEntryInterface
{
    /**
     *
     * @var integer
     */
    const CREATED = 0;

    /**
     *
     * @var integer
     */
    const ACTIVATED = 1;

    /**
     *
     * @var integer
     */
    const SUSPENDED = 2;

    /**
     *
     * @var integer
     */
    const KILLED = 3;

    /**
     *
     * @var integer
     */
    const COMPLETED = 4;

    /**
     *
     * @var integer
     */
    const UNKNOWN = -1;

    /**
     * Возвращает уникальный id, сущности для которой описывается workflow
     *
     * @return integer
     */
    public function getId();

    /**
     * Возвращает true, если сущность инициализирована
     *
     * @return boolean
     */
    public function isInitialized();

    /**
     * @return integer
     */
    public function getState();

    /**
     * Возвращает имя workflow, для данной сущности
     *
     * @return String
     */
    public function getWorkflowName();
}
