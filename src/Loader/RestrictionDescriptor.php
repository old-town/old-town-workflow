<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement; use SplObjectStorage;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class RestrictionDescriptor extends AbstractDescriptor
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
     * @return ConditionsDescriptor[]|SplObjectStorage
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
}
