<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use OldTown\Workflow\Loader\AbstractDescriptor;
use OldTown\Workflow\Loader\WriteXmlInterface;
use Behat\Gherkin\Node\PyStringNode;

/**
 * Defines application features from the specific context.
 */
class WorkflowDescriptorContext implements Context, SnippetAcceptingContext
{
    /**
     *
     * @var string
     */
    protected $workflowDescriptorNamespace = 'OldTown\Workflow\Loader';

    /**
     * @Given Create :nameDescriptor based on xml:
     *
     * @param string             $nameDescriptor
     * @param PyStringNode $xml
     *
     * @throws RuntimeException
     */
    public function createDescriptorByNameBasedOnXml($nameDescriptor, PyStringNode $xml)
    {
        try {
            $xmlDoc = new \DOMDocument();
            $xmlDoc->loadXML($xml->getRaw());

            $descriptor = $this->factoryDescriptor($nameDescriptor, $xmlDoc->firstChild);


        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * Наймспейс в котором находятся дескрипторы Workflow
     *
     * @return string
     */
    public function getWorkflowDescriptorNamespace()
    {
        return $this->workflowDescriptorNamespace;
    }

    /**
     * Фабрика по созданию дескрипторов
     *
     * @param string     $name
     *
     * @param DOMElement $element
     *
     * @return AbstractDescriptor
     * @throws RuntimeException
     */
    protected function factoryDescriptor($name, DOMElement $element = null)
    {
        $ns = $this->getWorkflowDescriptorNamespace();
        $class = "{$ns}\\{$name}";

        if (!class_exists($class)) {
            $errMsg = "Class not found {$class}";
            throw new \RuntimeException($errMsg);
        }

        $r = new \ReflectionClass($class);
        if (null === $element) {
            $descriptor = $r->newInstance();
        } else {
            $descriptor = $r->newInstanceArgs([
                $element
            ]);
        }

        if (!$descriptor instanceof AbstractDescriptor) {
            $errMsg = 'Descriptor not instance of AbstractDescriptor';
            throw new \RuntimeException($errMsg);
        }

        return $descriptor;
    }

}
