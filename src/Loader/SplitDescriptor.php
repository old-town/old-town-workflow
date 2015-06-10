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
class SplitDescriptor extends AbstractDescriptor
{
    use Traits\IdTrait;

    /**
     * @var ResultDescriptor[]|SplObjectStorage
     */
    protected $results;


    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        $this->results = new SplObjectStorage();

        parent::__construct($element);

        if (null !== $element) {
            $this->init($element);
        }
    }

    /**
     * @param DOMElement $split
     *
     * @return void
     */
    protected function init(DOMElement $split)
    {
        $this->parseId($split);

        $uResults = XMLUtil::getChildElements($split, 'unconditional-result');

        foreach ($uResults as $result) {
            $resultDescriptor = new ResultDescriptor($result);
            $resultDescriptor->setParent($this);
            $this->results->attach($resultDescriptor);
        }
    }

    /**
     * @return ResultDescriptor[]|SplObjectStorage
     */
    public function getResults()
    {
        return $this->results;
    }
}
