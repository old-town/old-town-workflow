<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use DOMNode;
use SplObjectStorage;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ConditionsDescriptor extends AbstractDescriptor
{

    /**
     * @var ConditionsDescriptor[]|SplObjectStorage
     */
    private $conditions;

    /**
     * Тип условий
     *
     * @var string
     */
    private $type;

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        $this->conditions = new SplObjectStorage();

        if (null !== $element) {
            $this->init($element);
        }

        parent::__construct($element);
    }

    /**
     * @param DOMElement $element
     *
     * @return void
     */
    protected function init(DOMElement $element)
    {
        if ($element->hasAttribute('type')) {
            $this->type = XmlUtil::getRequiredAttributeValue($element, 'type');
        }

        for ($i = 0; $i < $element->childNodes->length; $i++) {
            /** @var DOMElement $child */
            $child = $element->childNodes->item($i);

            if ($child instanceof DOMNode) {
                if ('condition' === $child->nodeName) {
                    $condition = DescriptorFactory::getFactory()->createConditionDescriptor($child);
                    $this->conditions->attach($condition);
                } elseif ('conditions' === $child->nodeName) {
                    $condition = DescriptorFactory::getFactory()->createConditionsDescriptor($child);
                    $this->conditions->attach($condition);
                }
            }
        }
    }

    /**
     * Возвращает тип условий
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Устанавливает тип условий
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
     * @return ConditionsDescriptor[]|SplObjectStorage
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param SplObjectStorage $conditions
     *
     * @return $this
     */
    public function setConditions(SplObjectStorage $conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

}
