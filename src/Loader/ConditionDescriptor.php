<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use DOMDocument;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;


/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ConditionDescriptor extends AbstractDescriptor
    implements
        Traits\ArgsInterface,
        Traits\TypeInterface,
        Traits\NameInterface,
        Traits\CustomArgInterface,
        WriteXmlInterface,
        ValidateDescriptorInterface
{
    use Traits\ArgsTrait, Traits\TypeTrait, Traits\IdTrait, Traits\NameTrait;


    /**
     *  Если true, то результат условия инвертируется на противоположное булево значение
     *
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
        $this->parseType($condition);
        $this->parseId($condition, false);
        $this->parseName($condition, false);

        $this->parseArgs($condition);

        if ($condition->hasAttribute('negate')) {
            $n =  XmlUtil::getRequiredAttributeValue($condition, 'negate');
            $nNormalize = strtolower($n);

            $this->negate = ('true' === $nNormalize || 'yes' === $nNormalize);
        }
    }

    /**
     * Если true, то результат условия инвертируется на противоположное булево значение
     *
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
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement
     * @throws InvalidDescriptorException
     * @throws \OldTown\Workflow\Exception\InvalidWriteWorkflowException
     */
    public function writeXml(DOMDocument $dom = null)
    {
        if (null === $dom) {
            $errMsg = 'Не передан DOMDocument';
            throw new InvalidWriteWorkflowException($errMsg);
        }

        $descriptor = $dom->createElement('condition');


        if ($this->hasId()) {
            $id = $this->getId();
            $descriptor->setAttribute('id', $id);
        }

        $name = $this->getName();
        if (null !== $name && is_string($name) && strlen($name) > 0) {
            $descriptor->setAttribute('name', $name);
        }

        $type = $this->getType();
        if (null === $type) {
            $errMsg = 'Некорректное значение для атрибута type';
            throw new InvalidDescriptorException($errMsg);
        }

        $descriptor->setAttribute('type', $type);

        if ($this->isNegate()) {
            $descriptor->setAttribute('negate', 'true');
        }

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
        $flag = 'phpshell' === $this->getType();

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

    /**
     * Валидация дескриптора
     *
     * @return void
     * @throws InvalidWorkflowDescriptorException
     */
    public function validate()
    {
    }
}
