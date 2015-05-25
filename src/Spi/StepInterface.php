<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Spi;

use DateTime;

/**
 * Interface WorkflowContextInterface
 *
 * @package OldTown\Workflow\Spi\StepInterface
 */
interface StepInterface
{
    /**
     * Возвращает идентификатор действия, связанного с этим шагом. Либо 0, если нет никаких связанных действия.
     *
     * @return integer
     */
    public function getActionId();

    /**
     * @return string
     */
    public function getCaller();

    /**
     * Возвращает дополнительную дату, обозначающую, когда этот шаг должен быть завершен
     *
     * @return DateTime
     */
    public function getDueDate();

    /**
     * Возвращает уникальный id экземпляра рабочего процесса
     *
     * @return integer
     */
    public function getEntryId();

    /**
     * Возвращает дату, когда данный шаг был закончен. Либо 0, если шаг еще не закончен
     *
     * @return DateTime
     */
    public function getFinishDate();

    /**
     * Возвращает id данного шага
     *
     * @return integer
     */
    public function getId();

    /**
     * Возвращает id владельца данного шага, или 0 если нет владельца
     *
     * @return string
     */
    public function getOwner();

    /**
     * Возвращает id предыдущего шага, или 0 если это первый шаг
     *
     * @return int[]
     */
    public function getPreviousStepIds();

    /**
     * Возвращает дату, когда данный шаг был создан
     *
     * @return DateTime
     */
    public function getStartDate();

    /**
     * Возвращает статус данного шага
     *
     * @return String
     */
    public function getStatus();

    /**
     * Возвращает идентификатор шага, в определении workflow
     *
     * @return integer
     */
    public function getStepId();
}
