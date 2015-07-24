<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use OldTown\Workflow\Loader\AbstractDescriptor;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Tester\Result\ExecutedStepResult;

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
     * Последний созданный дескриптор
     *
     * @var AbstractDescriptor
     */
    protected $lastCreatedDescriptor;

    /**
     * @var
     */
    protected $currentScenario;

    /**
     * @Given Create :nameDescriptor based on xml:
     *
     * @param string       $nameDescriptor
     * @param PyStringNode $xml
     *
     * @return AbstractDescriptor
     * @throws RuntimeException
     */
    public function createDescriptorByNameBasedOnXml($nameDescriptor, PyStringNode $xml)
    {
        try {
            $xmlDoc = new \DOMDocument();
            $xmlDoc->loadXML($xml->getRaw());
            $descriptor = $this->factoryDescriptor($nameDescriptor, $xmlDoc->firstChild);
            return $descriptor;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @Then Call a method descriptor :nameMethod, I get the value of :expectedResult
     *
     * @param $nameMethod
     * @param $expectedResult
     *
     * @throws \RuntimeException
     */
    public function callAMethodDescriptorIGetTheValueOf($nameMethod, $expectedResult)
    {
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            $r = new \ReflectionObject($descriptor);

            if (!$r->hasMethod($nameMethod)) {
                $errMsg = "Method {$nameMethod}  does not exist";
                throw new \InvalidArgumentException($errMsg);
            }

            $actualValue = $r->getMethod($nameMethod)->invoke($descriptor);



            $errMsg = sprintf(
                "Bug with attribute of \"variable-name\". Expected value: %s. Actual value: %s",
                $expectedResult,
                $actualValue
            );

            PHPUnit_Framework_Assert::assertEquals($expectedResult, $actualValue, $errMsg);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }



    /**
     * @BeforeScenario @workflowDescriptor
     */
    public function beforeScenarioWithTagWorkflowDescriptor()
    {
        $this->lastCreatedDescriptor = null;
    }

    /**
     * @AfterStep
     *
     * @param AfterStepScope $scope
     */
    public function afterStepWithTagWorkflowDescriptor(AfterStepScope $scope)
    {
        if ($this->currentScenario instanceof ScenarioInterface && $this->currentScenario->hasTag('workflowDescriptor')) {
            $result = $scope->getTestResult();
            if ($result instanceof ExecutedStepResult) {
                $descriptor = $result->getCallResult()->getReturn();
                if ($descriptor instanceof AbstractDescriptor) {
                    $this->lastCreatedDescriptor = $descriptor;
                }
            }
        }
    }

    /**
     * Возвращает последний созданный дескриптор
     *
     * @return AbstractDescriptor
     *
     * @throws \RuntimeException
     */
    protected function getLastCreatedDescriptor()
    {
        if (!$this->lastCreatedDescriptor instanceof AbstractDescriptor) {
            $errMsg = 'Descriptor does not exist';
            throw new \RuntimeException($errMsg);
        }
        return $this->lastCreatedDescriptor ;
    }



    /**
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     */
    public function beforeScenario(BeforeScenarioScope $scope)
    {
        $this->currentScenario = $scope->getScenario();
    }


    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $this->currentScenario = null;
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
