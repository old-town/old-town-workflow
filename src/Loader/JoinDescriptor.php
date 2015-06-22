<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;
use SplObjectStorage;
use DOMDocument;


/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class JoinDescriptor extends AbstractDescriptor  implements ValidateDescriptorInterface, WriteXmlInterface
{
    use Traits\IdTrait;

    /**
     * @var ConditionsDescriptor[]|SplObjectStorage
     */
    protected $conditions;

    /**
     * @var ResultDescriptor
     */
    protected $result;


    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        $this->conditions = new SplObjectStorage();

        parent::__construct($element);


        if (null !== $element) {
            $this->init($element);
        }
    }

    /**
     * @param DOMElement $join
     *
     * @return void
     */
    protected function init(DOMElement $join)
    {
        $this->parseId($join);

        $conditionNodes = XmlUtil::getChildElements($join, 'conditions');
        foreach ($conditionNodes as $condition) {
            $conditionDescriptor = DescriptorFactory::getFactory()->createConditionsDescriptor($condition);
            $conditionDescriptor->setParent($conditionDescriptor);
            $this->conditions->attach($conditionDescriptor);
        }

        $resultElement = XMLUtil::getChildElement($join, 'unconditional-result');
        if (null !== $resultElement) {
            $this->result = new ResultDescriptor($resultElement);
            $this->result->setParent($this);
        }
    }

    /**
     * @return ConditionsDescriptor[]|SplObjectStorage
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return ResultDescriptor
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ResultDescriptor $result
     *
     * @return $this
     */
    public function setResult(ResultDescriptor $result)
    {
        $this->result = $result;

        return $this;
    }


    /**
     * Валидация дескриптора
     *
     * @return void
     * @throws InvalidWorkflowDescriptorException
     */
    public function validate()
    {
        $conditions = $this->getConditions();
        ValidationHelper::validate($conditions);

        $result = $this->getResult();
        if (null === $result) {
            $errMsg = 'Join должен иметь реузультат';
            throw new InvalidWorkflowDescriptorException($errMsg);
        }

        $result->validate();
    }

    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement|null
     * @throws InvalidDescriptorException
     * @throws InvalidWriteWorkflowException
     */
    public function writeXml(DOMDocument $dom = null)
    {
        $descriptor = $dom->createElement('join');

        if (!$this->hasId()) {
            $errMsg = 'Отсутствует атрибут id';
            throw new InvalidDescriptorException($errMsg);
        }
        $id =  $this->getId();
        $descriptor->setAttribute('id',$id);

        $conditions = $this->getConditions();
        foreach ($conditions as $condition) {
            $conditionElement = $condition->writeXml($dom);
            if (null !== $conditionElement) {
                $descriptor->appendChild($conditionElement);
            }
        }

        $result = $this->getResult();

        if (null !== $result) {
            $resultElement = $result->writeXml($dom);
            $descriptor->appendChild($resultElement);
        }

        return $descriptor;

    }
}
