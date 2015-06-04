<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use SplObjectStorage;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ConditionalResultDescriptor extends ResultDescriptor
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
        if (null !== $element) {
            $this->init($element);
        }

        parent::__construct($element);
    }

    /**
     * @param DOMElement $conditionalResult
     *
     * @return void
     */
    protected function init(DOMElement $conditionalResult)
    {
        parent::init($conditionalResult);

        $conditionNodes = XMLUtil::getChildElements($conditionalResult, 'conditions');
        foreach ($conditionNodes as $condition) {
            $conditionDescriptor = DescriptorFactory::getFactory()->createConditionsDescriptor($condition);
            $conditionDescriptor->setParent($this);
            $this->conditions->attach($conditionDescriptor);
        }
    }

    /**
     * @return ConditionsDescriptor[]|SplObjectStorage
     */
    public function getConditions()
    {
        return $this->conditions;
    }

}
