<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;
use SplObjectStorage;
use DOMDocument;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class RestrictionDescriptor extends AbstractDescriptor implements ValidateDescriptorInterface, WriteXmlInterface
{
    /**
     * @var ConditionsDescriptor[]|SplObjectStorage
     */
    protected $conditions;

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
     * @return ConditionsDescriptor[]|SplObjectStorage
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param DOMElement $restriction
     *
     * @return void
     */
    protected function init(DOMElement $restriction)
    {
        $conditionNodes = XmlUtil::getChildElements($restriction, 'conditions');
        foreach ($conditionNodes as $condition) {
            $conditionDescriptor = DescriptorFactory::getFactory()->createConditionsDescriptor($condition);
            $conditionDescriptor->setParent($this);
            $this->conditions->attach($conditionDescriptor);
        }
    }

    /**
     * @return ConditionsDescriptor
     */
    public function getConditionsDescriptor()
    {
        if (0 ===  $this->conditions->count()) {
            return null;
        }
        return $this->conditions->current();
    }


    /**
     * @param ConditionsDescriptor $descriptor
     *
     * @return $this
     */
    public function setConditionsDescriptor(ConditionsDescriptor $descriptor)
    {
        if (1 ===  $this->conditions->count()) {
            $currentObj = $this->conditions->current();
            $this->conditions->detach($currentObj);
            $this->conditions->attach($descriptor);
        } else {
            $this->conditions->attach($descriptor);
        }
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
        $countConditions = $conditions->count();

        if ($countConditions >1) {
            $errMsg = 'Restriction может иметь только один вложенный Condition';
            throw new InvalidWorkflowDescriptorException($errMsg);
        }
        ValidationHelper::validate($conditions);
    }

    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement|null
     * @throws InvalidWriteWorkflowException
     */
    public function writeXml(DOMDocument $dom = null)
    {
        if (null === $dom) {
            $errMsg = 'Не передан DOMDocument';
            throw new InvalidWriteWorkflowException($errMsg);
        }
        $conditions = $this->getConditionsDescriptor();

        $list = $conditions->getConditions();

        if (0 === $list->count()) {
            return null;
        }

        $descriptor = $dom->createElement('restrict-to');
        try {
            $conditionsDescriptor = $conditions->writeXml($dom);
        } catch (\Exception $e) {
            $errMsg  = 'Ошибка сохранения workflow';
            throw new InvalidWriteWorkflowException($errMsg, $e->getCode(), $e);
        }

        $descriptor->appendChild($conditionsDescriptor);

        return $descriptor;
    }
}
