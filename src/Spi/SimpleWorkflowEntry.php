<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Spi;

use OldTown\Workflow\Exception\ArgumentNotNumericException;
use Serializable;

/**
 * Class SimpleWorkflowEntry
 * @package OldTown\Workflow\Spi
 */
class SimpleWorkflowEntry implements Serializable, WorkflowEntryInterface
{
    /**
     * Имя workflow
     *
     * @var string
     */
    protected $workflowName;

    /**
     * Флаг определяющий было ли инициированно workflow
     *
     * @var boolean
     */
    protected $initialized;

    /**
     * id определяющий состояние
     *
     * @var integer
     */
    protected $state;

    /**
     * Уникальынй идендификатор
     *
     * @var integer
     */
    protected $id;

    /**
     * @param integer $id
     * @param string $workflowName
     * @param integer $state
     */
    public function __construct($id, $workflowName, $state)
    {
        $this->id = (integer)$id;
        $this->state = (integer)$state;
        $this->workflowName = (string)$workflowName;
    }


    /**
     * Возвращает уникальный id, сущности для которой описывается workflow
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Устанавливает id
     *
     * @param int $id
     * @return $this
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
     * Устанавливает флаг указывающий на то что сущность была иницилизирована
     *
     * @param boolean $initialized
     * @return $this
     */
    public function setInitialized($initialized)
    {
        $this->initialized = (boolean)$initialized;

        return $this;
    }

    /**
     * Возвращает true, если сущность инициализирована
     *
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Возвращает id определяющий состояние
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Устанавливает id определяющий состояние
     *
     * @param int $state
     *
     * @return $this
     */
    public function setState($state)
    {
        if (!is_numeric($state)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $state);
            throw new ArgumentNotNumericException($errMsg);
        }
        $this->state = (integer)$state;

        return $this;
    }


    /**
     * Возвращает имя workflow, для данной сущности
     *
     * @return String
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Устанавливает имя workflow
     *
     * @param string $workflowName
     * @return $this
     */
    public function setWorkflowName($workflowName)
    {
        $this->workflowName = (string)$workflowName;

        return $this;
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
