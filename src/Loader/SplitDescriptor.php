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
class SplitDescriptor extends AbstractDescriptor implements ValidateDescriptorInterface, WriteXmlInterface
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

    /**
     * Валидация дескриптора
     *
     * @return void
     * @throws InvalidWorkflowDescriptorException
     */
    public function validate()
    {
        $results = $this->getResults();
        ValidationHelper::validate($results);
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
        if (null === $dom) {
            $errMsg = 'Не передан DOMDocument';
            throw new InvalidWriteWorkflowException($errMsg);
        }
        $descriptor = $dom->createElement('split');

        if (!$this->hasId()) {
            $errMsg = 'Отсутствует атрибут id';
            throw new InvalidDescriptorException($errMsg);
        }
        $id =  $this->getId();
        $descriptor->setAttribute('id', $id);

        $results = $this->getResults();

        foreach ($results as $result) {
            $resultElement = $result->writeXml($dom);
            $descriptor->appendChild($resultElement);
        }

        return $descriptor;
    }
}
