<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Spi;

use OldTown\Workflow\Exception\ArgumentNotNumericException;
use Serializable;
use \DateTime;


/**
 * Interface WorkflowContextInterface
 *
 * @package OldTown\Workflow\Spi\StepInterface
 */
class SimpleStep implements StepInterface, Serializable
{
    /**
     *
     * @var DateTime|null
     */
    private $dueDate;

    /**
     *
     * @var DateTime|null
     */
    private $finishDate;

    /**
     *
     * @var DateTime
     */
    private $startDate;

    /**
     * @var string|null
     */
    private $caller;

    /**
     * @var string
     */
    private $owner;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $previousStepIds = [];

    /**
     * @var integer
     */
    private $actionId;

    /**
     * @var integer
     */
    private $stepId;

    /**
     * @var integer
     */
    private $entryId;

    /**
     * @var integer
     */
    private $id;

    public function __construct($id, $entryId, $stepId, $actionId, $owner, DateTime $startDate, DateTime $dueDate = null, DateTime $finishDate = null, $status, array $previousStepIds = [], $caller)
    {
        $this->setId($id);
        $this->setEntryId($entryId);
        $this->setStepId($stepId);
        $this->setActionId($actionId);
        $this->setOwner($owner);
        $this->setStartDate($startDate);
        $this->setFinishDate($finishDate);
        $this->setDueDate($dueDate);
        $this->setStatus($status);
        $this->setPreviousStepIds($previousStepIds);
        $this->setCaller($caller);
    }

    //~ Methods ////////////////////////////////////////////////////////////////

    /**
     * Устанавливает id действия
     *
     * @param integer $actionId
     * @return $this
     *
     * @throws ArgumentNotNumericException
     */
    public function setActionId($actionId)
    {
        if (!is_numeric($actionId)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $actionId);
            throw new ArgumentNotNumericException($errMsg);
        }
        $this->actionId = (integer)$actionId;
        return $this;
    }

    /**
     * Возвращает id действия
     *
     * @return integer
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * Устанавливает имя того кто вызвал действие приведшее к переходу на данный шаг
     *
     * @param $caller|null
     * @return $this
     */
    public function setCaller($caller = null)
    {
        $this->caller = (null !== $caller) ? (string)$caller : null;

        return $this;
    }

    /**
     * Возвращает имя того кто вызвал действие приведшее к переходу на данный шаг
     *
     * @return string|null
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * Устанавливает период
     *
     * @param DateTime $dueDate
     * @return $this
     */
    public function setDueDate(DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Возвращает период
     *
     * @return DateTime|null
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Устанавливает id экземпляра workflow
     *
     * @param integer $entryId
     * @return $this
     *
     * @throws ArgumentNotNumericException
     */
    public function setEntryId($entryId)
    {
        if (!is_numeric($entryId)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $entryId);
            throw new ArgumentNotNumericException($errMsg);
        }

        $this->entryId = (integer)$entryId;

        return $this;
    }

    /**
     * Возвращает id экземпляра workflow
     *
     * @return int
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    /**
     * Устанавливает дату окончания когда сущность прибывала в данном состояние
     *
     * @param DateTime $finishDate
     * @return $this
     */
    public function setFinishDate(DateTime $finishDate = null)
    {
        $this->finishDate = $finishDate;
        return $this;
    }

    /**
     * Возвращает дату окончания когда сущность прибывала в данном состояние
     *
     * @return DateTime|null
     */
    public function getFinishDate()
    {
        return $this->finishDate;
    }

    /**
     * Устанавливает id шага
     *
     * @param integer $id
     * @return $this
     *
     * @throws ArgumentNotNumericException
     */
    public function setId($id)
    {
        if (!is_numeric($id)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $id);
            throw new ArgumentNotNumericException($errMsg);
        }
        $this->id = (integer)$id;
        return $this;
    }

    /**
     * Возвращает id шага
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Устанавливает имя того кто является владельцем данного шага
     *
     * @param string $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = (string)$owner;
        return $this;
    }

    /**
     * Возвращает имя того кто является владельцем данного шага
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Устанавливает id предыдущих шагов
     *
     * @param array $previousStepIds
     * @return $this
     */
    public function setPreviousStepIds(array $previousStepIds = [])
    {
        $this->previousStepIds = $previousStepIds;

        return $this;
    }

    /**
     * Возвращает id предыдущих шагов
     *
     * @return array
     */
    public function getPreviousStepIds()
    {
        return $this->previousStepIds;
    }

    /**
     * Устанавливает дату, когда workflow перешло в данный шаг
     *
     * @param DateTime $startDate
     * @return $this
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Возвращает дату, когда workflow перешло в данный шаг
     *
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Устанавливает статус в котором находится шаг
     *
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = (string)$status;

        return $this;
    }

    /**
     * Возвращает статус в котором находится шаг
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Устанавливает id шгага
     *
     * @param integer $stepId
     * @return $this
     *
     * @throws ArgumentNotNumericException
     */
    public function setStepId($stepId)
    {
        if (!is_numeric($stepId)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $stepId);
            throw new ArgumentNotNumericException($errMsg);
        }
        $this->stepId = (integer)$stepId;

        return $this;
    }

    /**
     * Возвращает id шгага
     *
     * @return int
     */
    public function getStepId()
    {
        return $this->stepId;
    }

    /**
     * Отображение состояние шага в текстовом виде
     *
     * @return string
     */
    public function __toString()
    {
        $r = sprintf('SimpleStep@ %s[owner=%s, actionId=%s, status=%s]',
        $this->getStepId(),
        $this->getOwner(),
        $this->getActionId(),
        $this->getStatus());
        return $r;
    }

    /**
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string
     */
    public function serialize()
    {
        $errMsg = sprintf('Метод %s класса %s требуется реализовать', __METHOD__, __CLASS__);
        trigger_error($errMsg, E_USER_ERROR);
    }

    /**
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        $errMsg = sprintf('Метод %s класса %s требуется реализовать', __METHOD__, __CLASS__);
        trigger_error($errMsg, E_USER_ERROR);
    }
}
