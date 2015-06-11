<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader\Traits;

use DOMElement;
use OldTown\Workflow\Loader\XmlUtil;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
trait ArgsTrait
{
    /**
     * @var array
     */
   protected $args = [];

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
     * Парсит элемент и извлекает из него аргументы
     *
     * @param DOMElement $element
     * @return void
     */
    protected function parseArgs(DOMElement $element)
    {
        $args = XmlUtil::getChildElements($element, 'arg');
        foreach ($args as $arg) {
            $name = XmlUtil::getRequiredAttributeValue($arg, 'name');
            $this->args[$name] = XmlUtil::getText($element);
        }
    }

    /**
     * @param DOMElement $parent
     *
     * @return void
     */
    protected function writeArgs(DOMElement $parent)
    {
        $args = $this->getArgs();
        if (0 === count($args)) {
            return null;
        }

        $dom = $parent->ownerDocument;
        foreach ($args as $key => $value) {
            $arg = $dom->createElement('arg');
            $arg->setAttribute('name', $key);

            if ($this instanceof CustomArgInterface && $this->flagUseCustomArgWriter($key, $value)) {
                $this->buildArgValue($key, $value, $arg);
            } else {
                $valueArgElement = $dom->createTextNode($value);
                $arg->appendChild($valueArgElement);
            }

            $parent->appendChild($arg);
        }
    }
}
