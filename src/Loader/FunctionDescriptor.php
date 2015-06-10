<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use DOMDocument;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class FunctionDescriptor extends AbstractDescriptor
    implements Traits\ArgsInterface,
               Traits\TypeInterface,
               Traits\NameInterface
{
    use Traits\ArgsTrait, Traits\TypeTrait, Traits\IdTrait, Traits\NameTrait;

    /**
     * @param DOMElement $element
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
     * @return DOMElement
     * @throws InvalidDescriptorException
     */
    public function writeXml(DOMDocument $dom)
    {
        $descriptor = $dom->createElement('function');
        $type = $this->getType();
        if (null === $type) {
            $errMsg = 'Некорректное значение для атрибута type';
            throw new InvalidDescriptorException($errMsg);
        }

        $descriptor->setAttribute('type', $type);

        $id = $this->getId();
        if (null !== $id) {
            $descriptor->setAttribute('id', $id);
        }
        $name = $this->getName();
        if (null !== $id) {
            $descriptor->setAttribute('name', $name);
        }


        return $descriptor;
    }
}
