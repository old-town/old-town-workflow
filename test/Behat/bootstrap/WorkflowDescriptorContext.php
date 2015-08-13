<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use OldTown\Workflow\Loader\AbstractDescriptor;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Gherkin\Node\TableNode;
use OldTown\Workflow\Loader\WriteXmlInterface;


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
     * @Given Create descriptor :nameDescriptor
     *
     * @param $nameDescriptor
     *
     * @return AbstractDescriptor
     *
     * @throws \RuntimeException
     */
    public function createDescriptor($nameDescriptor)
    {
        try {
            $descriptor = $this->factoryDescriptor($nameDescriptor);

            return $descriptor;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @Given Create descriptor :nameDescriptor based on xml:
     *
     * @param string       $nameDescriptor
     * @param PyStringNode $xml
     *
     * @return AbstractDescriptor
     * @throws \RuntimeException
     */
    public function createDescriptorByNameBasedOnXml($nameDescriptor, PyStringNode $xml)
    {
        $useXmlErrors = libxml_use_internal_errors();
        try {
            libxml_use_internal_errors(true);
            libxml_clear_errors();

            $xmlDoc = new \DOMDocument();
            $xmlDoc->loadXML($xml->getRaw());

            $libxmlGetLastError = libxml_get_last_error();
            if ($libxmlGetLastError instanceof \LibXMLError) {
                throw new \RuntimeException($libxmlGetLastError->message, $libxmlGetLastError->code);
            }

            $descriptor = $this->factoryDescriptor($nameDescriptor, $xmlDoc->firstChild);

            libxml_use_internal_errors($useXmlErrors);

            return $descriptor;
        } catch (\Exception $e) {
            libxml_clear_errors();
            libxml_use_internal_errors($useXmlErrors);
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
                "Bug with attribute of \"%s\". Expected value: %s. Actual value: %s",
                $nameMethod,
                $expectedResult,
                $actualValue
            );

            PHPUnit_Framework_Assert::assertEquals($expectedResult, $actualValue, $errMsg);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @Transform /^\(.+?\).+?$/
     *
     * @param $expectedResult
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function intelligentTransformArgument($expectedResult)
    {
        $outputArray = [];
        preg_match_all('/^\((.+?)\)(.+?)$/', $expectedResult, $outputArray);


        if (3 !== count($outputArray)) {
            return $expectedResult;
        }

        if (!(is_array($outputArray[1]) && 1 === count($outputArray[1]))) {
            return $expectedResult;
        }


        if (!(is_array($outputArray[2]) && 1 === count($outputArray[2]))) {
            return $expectedResult;
        }

        $originalType = $outputArray[1][0];
        $type = strtolower($originalType);

        $value = $outputArray[2][0];

        $result = $value;
        switch ($type) {
            case 'boolean':
            case 'bool': {
                $prepareValue = trim($result);
                $prepareValue = strtolower($prepareValue);

                $falseStrings = [
                    ''      => '',
                    'false' => 'false',
                    '0'     => '0',
                ];

                $result = !array_key_exists($prepareValue, $falseStrings);

                break;
            }
            case 'null': {
                $result = '(null)null' === $expectedResult ? null : $expectedResult;
                break;
            }
            case '\\domdocument':
            case 'domdocument': {
                if ('(DOMDocument)domDocument' === $expectedResult) {
                    $result = new \DOMDocument();
                }

                break;
            }
            default: {

            }
        }

        return $result;
    }

    /**
     * @When Call a method descriptor :nameMethod. The arguments of the method:
     *
     * @param           $nameMethod
     * @param TableNode $table
     *
     * @throws RuntimeException
     */
    public function callAMethodDescriptorTheArgumentsOfTheMethod($nameMethod, TableNode $table)
    {
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            $r = new \ReflectionObject($descriptor);

            if (!$r->hasMethod($nameMethod)) {
                $errMsg = "Method {$nameMethod}  does not exist";
                throw new \InvalidArgumentException($errMsg);
            }

            $rows = $table->getHash();
            if (1 !== count($rows)) {
                $errMsg = 'Incorrect arguments';
                throw new \InvalidArgumentException($errMsg);
            }

            $args = $rows[0];

            $transformArg = [];
            foreach ($args as $index => $arg) {
                $transformArg[$index] = $this->intelligentTransformArgument($arg);
            }

            $r->getMethod($nameMethod)->invokeArgs($descriptor, $transformArg);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @Then Call a method descriptor :nameMethod. I expect to get an exception :expectedException
     *
     * @param string $nameMethod
     * @param string $expectedException
     */
    public function callAMethodDescriptorIExpectToGetAnException($nameMethod, $expectedException)
    {
        $actualException = null;
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            $r = new \ReflectionObject($descriptor);

            if (!$r->hasMethod($nameMethod)) {
                $errMsg = "Method {$nameMethod}  does not exist";
                throw new \InvalidArgumentException($errMsg);
            }

            $r->getMethod($nameMethod)->invoke($descriptor);
        } catch (\Exception $e) {
            $actualException = $e;
        }

        PHPUnit_Framework_Assert::assertInstanceOf($expectedException, $actualException);
    }

    /**
     * @Then Call a method descriptor :nameMethod. I expect to get an exception message :expectedExceptionMessage
     *
     * @param $nameMethod
     * @param $expectedExceptionMessage
     */
    public function callAMethodDescriptorIExpectToGetAnExceptionMessage($nameMethod, $expectedExceptionMessage)
    {
        $actualExceptionMessage = null;
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            $r = new \ReflectionObject($descriptor);

            if (!$r->hasMethod($nameMethod)) {
                $errMsg = "Method {$nameMethod}  does not exist";
                throw new \InvalidArgumentException($errMsg);
            }

            $r->getMethod($nameMethod)->invoke($descriptor);
        } catch (\Exception $e) {
            $actualExceptionMessage = $e->getMessage();
        }

        PHPUnit_Framework_Assert::assertEquals($expectedExceptionMessage, $actualExceptionMessage);
    }




    /**
     * @Then Call a method descriptor :nameMethod, I get the value of :expectedResult. The arguments of the method:
     *
     * @param string    $nameMethod
     * @param string    $expectedResult
     * @param TableNode $table
     *
     * @throws \RuntimeException
     *
     */
    public function callAMethodDescriptorIGetTheValueOfTheArgumentsOfTheMethod($nameMethod, $expectedResult, TableNode $table)
    {
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            $r = new \ReflectionObject($descriptor);

            if (!$r->hasMethod($nameMethod)) {
                $errMsg = "Method {$nameMethod}  does not exist";
                throw new \InvalidArgumentException($errMsg);
            }

            $rows = $table->getHash();
            if (1 !== count($rows)) {
                $errMsg = 'Incorrect arguments';
                throw new \InvalidArgumentException($errMsg);
            }

            $args = $rows[0];

            $transformArg = [];
            foreach ($args as $index => $arg) {
                $transformArg[$index] = $this->intelligentTransformArgument($arg);
            }


            $actualValue = $r->getMethod($nameMethod)->invokeArgs($descriptor, $transformArg);

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
     * @Then     I save to descriptor xml. Compare with xml:
     *
     * @param PyStringNode $expectedXmlNode
     *
     * @throws \RuntimeException
     *
     */
    public function iSaveToDescriptorXmlCompareWithXml(PyStringNode $expectedXmlNode)
    {
        try {
            $dom = new \DOMDocument();
            $dom->encoding = 'UTF-8';
            $dom->xmlVersion = '1.0';
            $dom->formatOutput = true;

            $descriptor = $this->getLastCreatedDescriptor();
            if (!$descriptor instanceof WriteXmlInterface) {
                $errMsg = 'Descriptor not implement WriteXmlInterface';
                throw new \RuntimeException($errMsg);
            }

            $result = $descriptor->writeXml($dom);

            if ($result instanceof \DOMDocument) {
                $actualXml = $result->saveXML();
            } elseif ($result instanceof \DOMElement) {
                $actualXml = $result->ownerDocument->saveXML($result);
            } else {
                $errMsg = 'Incorrect result writeXml';
                throw new \RuntimeException($errMsg);
            }

            $expectedXml = $expectedXmlNode->getRaw();

            PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString($expectedXml, $actualXml);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @Then I save to descriptor xml. I expect to get an exception :expectedException
     *
     * @param string $expectedException
     */
    public function iSaveToDescriptorXmlIExpectToGetAnException($expectedException)
    {
        $actualException = null;
        try {
            $dom = new \DOMDocument();
            $dom->encoding = 'UTF-8';
            $dom->xmlVersion = '1.0';
            $dom->formatOutput = true;

            $descriptor = $this->getLastCreatedDescriptor();
            if (!$descriptor instanceof WriteXmlInterface) {
                $errMsg = 'Descriptor not implement WriteXmlInterface';
                throw new \RuntimeException($errMsg);
            }

            $descriptor->writeXml($dom);
        } catch (\Exception $e) {
            $actualException = $e;
        }

        PHPUnit_Framework_Assert::assertInstanceOf($expectedException, $actualException);
    }

    /**
     * @Then     I save to descriptor xml. I expect to get an exception message :expectedException
     *
     * @param $expectedExceptionMessage
     *
     */
    public function iSaveToDescriptorXmlIExpectToGetAnExceptionMessage($expectedExceptionMessage)
    {
        $actualExceptionMessage = null;
        try {
            $dom = new \DOMDocument();
            $dom->encoding = 'UTF-8';
            $dom->xmlVersion = '1.0';
            $dom->formatOutput = true;

            $descriptor = $this->getLastCreatedDescriptor();
            if (!$descriptor instanceof WriteXmlInterface) {
                $errMsg = 'Descriptor not implement WriteXmlInterface';
                throw new \RuntimeException($errMsg);
            }

            $descriptor->writeXml($dom);
        } catch (\Exception $e) {
            $actualExceptionMessage = $e->getMessage();
        }

        PHPUnit_Framework_Assert::assertEquals($expectedExceptionMessage, $actualExceptionMessage);
    }



    /**
     * @Then I validated descriptor. I expect to get an exception message :expectedExceptionMessage
     *
     * @param $expectedExceptionMessage
     *
     */
    public function iValidatedDescriptorIExpectToGetAnExceptionMessage($expectedExceptionMessage)
    {
        $actualExceptionMessage = null;
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            if (!method_exists($descriptor, 'validate')) {
                $errMsg = 'Descriptor does not support validation';
                throw new \RuntimeException($errMsg);
            }

            call_user_func([$descriptor, 'validate']);
        } catch (\Exception $e) {
            $actualExceptionMessage = $e->getMessage();
        }

        PHPUnit_Framework_Assert::assertEquals($expectedExceptionMessage, $actualExceptionMessage);
    }


    /**
     * @Then I validated descriptor. I expect to get an exception :expectedException
     *
     * @param string $expectedException
     */
    public function iValidatedDescriptorIExpectToGetAnException($expectedException)
    {
        $actualException = null;
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            if (!method_exists($descriptor, 'validate')) {
                $errMsg = 'Descriptor does not support validation';
                throw new \RuntimeException($errMsg);
            }

            call_user_func([$descriptor, 'validate']);
        } catch (\Exception $e) {
            $actualException = $e;
        }

        PHPUnit_Framework_Assert::assertInstanceOf($expectedException, $actualException);
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

        return $this->lastCreatedDescriptor;
    }


    /**
     * @BeforeScenario
     *
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

    /**
     * @Given Get the descriptor using the method of :methodName
     *
     * @param $methodName
     *
     * @return AbstractDescriptor
     * @throws RuntimeException
     */
    public function getTheDescriptorUsingTheMethodOf($methodName)
    {
        try {
            $descriptor = $this->getLastCreatedDescriptor();
            if (!method_exists($descriptor, $methodName)) {
                $errMsg = "Descriptor does not support method {$methodName}";
                throw new \RuntimeException($errMsg);
            }

            $descriptors = call_user_func([$descriptor, $methodName]);

            $targetDescriptor = $descriptors;
            if ((is_array($descriptors) || $descriptors instanceof \Traversable) && 1 === count($descriptors)) {
                $iterator = is_array($descriptors) ? $descriptors : iterator_to_array($descriptors);

                $targetDescriptor = $iterator[0];
            }

            if (!$targetDescriptor instanceof AbstractDescriptor) {
                $errMsg = 'Descriptor not instance of AbstractDescriptor';
                throw new \RuntimeException($errMsg);
            }

            return $targetDescriptor;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
