<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use DOMDocument;
use OldTown\Workflow\Exception\InvalidDescriptorException;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ValidatorDescriptor extends AbstractDescriptor
    implements Traits\ArgsInterface,
               Traits\TypeInterface,
               Traits\NameInterface,
               Traits\CustomArgInterface,
               WriteXmlInterface
{
    use Traits\ArgsTrait, Traits\TypeTrait, Traits\IdTrait, Traits\NameTrait;

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
     * @param DOMElement $element
     *
     * @return void
     */
    protected function init(DOMElement $element)
    {
        $this->parseType($element);
        $this->parseId($element, false);
        $this->parseName($element, false);

        $this->parseArgs($element);
    }


    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement
     * @throws InvalidDescriptorException
     */
    public function writeXml(DOMDocument $dom = null)
    {
        $descriptor = $dom->createElement('validator');


        if ($this->hasId()) {
            $id = $this->getId();
            $descriptor->setAttribute('id', $id);
        }

        $name = $this->getName();
        if (null !== $name) {
            $nameEncode = XmlUtil::encode($name);
            $descriptor->setAttribute('name', $nameEncode);
        }

        $type = $this->getType();
        if (null === $type) {
            $errMsg = 'Некорректное значение для атрибута type';
            throw new InvalidDescriptorException($errMsg);
        }

        $descriptor->setAttribute('type', $type);

        $this->writeArgs($descriptor);

        return $descriptor;
    }


    /**
     * @param string $key
     * @param string $value
     *
     * @return boolean
     */
    public function flagUseCustomArgWriter($key, $value)
    {
        $flag = 'php-eval' === $this->getType();

        return $flag;
    }

    /**
     * Генерирует значение аргумента
     *
     * @param            $key
     * @param            $value
     *
     * @param DOMElement $argElement
     *
     * @return string
     */
    public function buildArgValue($key, $value, DOMElement $argElement)
    {
        $dom = $argElement->ownerDocument;
        $argValueElement = $dom->createCDATASection($value);
        $argElement->appendChild($argValueElement);
    }
}
