<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Exception;

/**
 * Class InvalidActionException
 *
 * @package OldTown\Workflow\Exception
 */
class NotExistsRequiredAttributeException extends InvalidParsingWorkflowException
{
    /**
     * Имя обязательного аттрибута
     *
     * @var string
     */
    protected $requiredAttributeName;

    /**
     * @return string
     */
    public function getRequiredAttributeName()
    {
        return $this->requiredAttributeName;
    }

    /**
     * @param string $requiredAttributeName
     *
     * @return $this
     */
    public function setRequiredAttributeName($requiredAttributeName)
    {
        $this->requiredAttributeName = (string)$requiredAttributeName;

        return $this;
    }
}
