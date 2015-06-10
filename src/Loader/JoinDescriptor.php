<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use SplObjectStorage;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class JoinDescriptor extends AbstractDescriptor
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
}
