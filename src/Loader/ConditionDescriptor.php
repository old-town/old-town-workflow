<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ConditionDescriptor extends AbstractDescriptor
{
    /**
     * Аргументы
     *
     * @var array
     */
    protected $args = [];

    /**
     * Имя условия
     *
     * @var string
     */
    protected $name;

    /**
     * Тип условия
     *
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $negate = false;

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {

        parent::__construct($element);

        if (null !== $element) {
            $this->init($element);
        }


    }

    /**
     * @param DOMElement $condition
     *
     * @return void
     */
    protected function init(DOMElement $condition)
    {
        $this->type = XmlUtil::getRequiredAttributeValue($condition, 'type');

        if ($condition->hasAttribute('id')) {
            $id = XmlUtil::getRequiredAttributeValue($condition, 'id');
            $this->setId($id);
        }


        if ($condition->hasAttribute('negate')) {
            $n =  XmlUtil::getRequiredAttributeValue($condition, 'negate');
            $nNormalize = strtolower($n);
            if ('true' === $nNormalize || 'yes' === $nNormalize) {
                $this->negate = true;
            } else {
                $this->negate = false;
            }
        }

        if ($condition->hasAttribute('name')) {
            $this->name =  XmlUtil::getRequiredAttributeValue($condition, 'name');
        }

        $args = XmlUtil::getChildElements($condition, 'arg');
        foreach ($args as $arg) {
            $name = XmlUtil::getRequiredAttributeValue($arg, 'name');
            $value = XmlUtil::getText($arg);

            $this->args[$name] = $value;
        }
    }

    /**
     * Возвращает тип условия
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Устанавливает тип условия
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = (string)$type;

        return $this;
    }

    /**
     * Возвращает имя условия
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Устанавливает имя условия
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNegate()
    {
        return $this->negate;
    }

    /**
     * @param boolean $negate
     *
     * @return $this
     */
    public function setNegate($negate)
    {
        $this->negate = (boolean)$negate;

        return $this;
    }

    /**
     * Возвращает аргументы
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Устанавливает аргумент
     *
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setArg($name, $value)
    {
        $this->args[$name] = $value;

        return $this;
    }

    /**
     * Устанавливает аргумент
     *
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return string
     */
    public function getArg($name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->args)) {
            return $this->args[$name];
        }

        return $defaultValue;
    }

}
