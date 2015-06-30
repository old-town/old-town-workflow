<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\JoinNodes;

/**
 * Class DummyStep
 *
 * @package OldTown\Workflow\JoinNodes
 */
class DummyStep
{
    /**
     * @return int
     */
    public function getActionId()
    {
        return -1;
    }

    /**
     * @return null
     */
    public function getCaller()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getDueDate()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getEntryId()
    {
        return -1;
    }

    /**
     * @return null
     */
    public function getFinishDate()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return -1;
    }

    /**
     * @return null
     */
    public function getOwner()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getPreviousStepIds()
    {
        return [];
    }

    /**
     * @return null
     */
    public function getStartDate()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return null;
    }


    /**
     * @return int
     */
    public function getStepId()
    {
        return -1;
    }
}
