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
            if ('true' === $nNormalize || 'yes' === $nNormalize) {
                $this->negate = true;
            } else {
                $this->negate = false;
            }
        }

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
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement
     * @throws InvalidDescriptorException
     */
    public function writeXml(DOMDocument $dom)
    {
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
