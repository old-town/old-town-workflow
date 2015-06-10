<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
abstract class AbstractDescriptor
{
    /**
     * Родительский дескрипторв
     *
     * @var AbstractDescriptor
     */
    protected $parent;

    /**
     * Флаг определяющий есть ли id у дескриптора
     *
     * @var bool
     */
    protected $hasId =false;

    /**
     * id дескриптора
     *
     * @var integer
     */
    protected $id;

    /**
     * @param DOMElement $element
     */
    public function __construct(DOMElement $element = null)
    {
    }

    /**
     * Устанавливает родительский дескриптор
     *
     * @param AbstractDescriptor $parent
     *
     * @return $this
     */
    public function setParent(AbstractDescriptor $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Возвращает родительский дескриптор
     *
     * @return AbstractDescriptor
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return boolean
     */
    public function hasId()
    {
        return $this->hasId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (integer)$id;
        $this->hasId = true;

        return $this;
    }
}
