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
class RegisterDescriptor extends AbstractDescriptor
{

    /**
     * Аргументы
     *
     * @var array
     */
    protected $args = [];

    /**
     * Тип
     *
     * @var string
     */
    protected $type;

    /**
     * Имя переменной
     *
     * @var string
     */
    protected $variableName;

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
     * @param DOMElement $register
     *
     * @return void
     */
    protected function init(DOMElement $register)
    {
        $this->type = XmlUtil::getRequiredAttributeValue($register, 'type');
        $this->variableName = XmlUtil::getRequiredAttributeValue($register, 'variable-name');

        if ( $register->hasAttribute('id')) {
            $id = XmlUtil::getRequiredAttributeValue($register, 'id');
            $this->setId($id);
        }


        $args = XmlUtil::getChildElements($register, 'arg');
        foreach ($args as $arg) {
            $name = XmlUtil::getRequiredAttributeValue($arg, 'name');
            $this->args[$name] = $arg->nodeValue;
        }

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

    /**
     * Возвращает тип
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Устанавливает тип
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Возвращает имя переменной
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Устанавливает имя переменной
     *
     * @param string $variableName
     *
     * @return $this
     */
    public function setVariableName($variableName)
    {
        $this->variableName = (string)$variableName;

        return $this;
    }
}
