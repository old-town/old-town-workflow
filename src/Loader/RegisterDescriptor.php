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
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class RegisterDescriptor extends AbstractDescriptor
    implements Traits\ArgsInterface,
    Traits\TypeInterface,
    Traits\CustomArgInterface,
    WriteXmlInterface
{
    use Traits\ArgsTrait, Traits\TypeTrait, Traits\IdTrait;

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
        $this->parseType($register);
        $this->variableName = XmlUtil::getRequiredAttributeValue($register, 'variable-name');

        $this->parseId($register, false);


        $this->parseArgs($register);
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
        $descriptor = $dom->createElement('register');


        if ($this->hasId()) {
            $id = $this->getId();
            $descriptor->setAttribute('id', $id);
        }

        $variableName = $this->getVariableName();
        if (null === $variableName) {
            $errMsg = 'Некорректное значение для атрибута variable-name';
            throw new InvalidDescriptorException($errMsg);
        }
        $descriptor->setAttribute('variable-name', $variableName);

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
