<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Basic\BasicWorkflow;
use OldTown\Workflow\Config\ArrayConfiguration;
use OldTown\Workflow\Spi\Memory\MemoryWorkflowStore;
use OldTown\Workflow\Loader\CallbackWorkflowFactory;
use OldTown\Workflow\Util\Properties\Properties;
use Behat\Gherkin\Node\TableNode;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\TransientVars\BaseTransientVars;

/**
 * Class WorkflowEngineContext
 */
class WorkflowEngineContext implements Context, SnippetAcceptingContext
{
    /**
     * @var array
     */
    protected $workflows = [];

    /**
     * @var null|callable
     */
    protected $callbackFactory;

    /**
     * @var BasicWorkflow
     */
    protected $workflowManager;

    /**
     * @var bool
     */
    protected $flagInitWorkflowManager = false;

    /**
     * Ключем является alias для идендфикатора запущенного процесса workflow. А значением id этого процесса
     *
     * @var array
     */
    protected $entryAliasToEntryId = [];

    /**
     * @var Exception|null
     */
    protected $lastException;

    /**
     * @var TransientVarsInterface
     */
    protected $transientVars;

    /**
     *
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @return void
     */
    protected function init()
    {
        $this->callbackFactory = function (WorkflowDescriptor $descriptor) {
            return function () use ($descriptor) {
                return $descriptor;
            };
        };
        $this->transientVars = new BaseTransientVars();
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenarioHandler()
    {
        $this->workflows = [];
        $this->workflowManager = null;
        $this->flagInitWorkflowManager = false;
        $this->entryAliasToEntryId = [];
        $this->lastException = null;
        $this->transientVars = new BaseTransientVars();
    }

    /**
     * @Given : Registrate the workflow with the name :workflowName. With xml:
     *
     * @param string             $workflowName
     * @param PyStringNode $xml
     *
     * @throws RuntimeException
     */
    public function registerAWorkflowWithTheNameWithXml($workflowName, PyStringNode $xml)
    {
        if (true === $this->flagInitWorkflowManager) {
            $errMsg = 'The action can only be performed to create a workflow manager';
            throw new \RuntimeException($errMsg);
        }

        $descriptor = $this->createWorkflowDescriptor($xml);
        if (array_key_exists($workflowName, $this->workflows)) {
            $errMsg = sprintf('Workflow %s already exists', $workflowName);
            throw new \RuntimeException($errMsg);
        }

        $this->workflows[$workflowName] = [
            'callback' => call_user_func($this->callbackFactory, $descriptor)
        ];
    }

    /**
     * @param PyStringNode $xml
     *
     * @return WorkflowDescriptor
     *
     * @throws RuntimeException
     */
    public function createWorkflowDescriptor(PyStringNode $xml)
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

            $rootCollection = $xmlDoc->getElementsByTagName('workflow');
            if (1 !== $rootCollection->length) {
                $errMsg = 'Incorrect structure workflow';
                throw new \RuntimeException($errMsg);
            }
            /** @var DOMElement $root */
            $root = $rootCollection->item(0);

            $r = new \ReflectionClass(WorkflowDescriptor::class);
            /** @var WorkflowDescriptor $descriptor */
            $descriptor = $r->newInstanceArgs([
                $root
            ]);

            libxml_use_internal_errors($useXmlErrors);
        } catch (\Exception $e) {
            libxml_clear_errors();
            libxml_use_internal_errors($useXmlErrors);
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        return $descriptor;
    }

    /**
     * @Given Create workflow manager
     *
     * @throws RuntimeException
     */
    public function createWorkflowManager()
    {
        try {
            $workflowManager = new BasicWorkflow(false);

            $properties = new  Properties();
            $properties->setProperty(CallbackWorkflowFactory::WORKFLOWS_PROPERTY, $this->workflows);
            $factory = new CallbackWorkflowFactory($properties);

            $configuration = new ArrayConfiguration([
                ArrayConfiguration::PERSISTENCE => MemoryWorkflowStore::class,
                ArrayConfiguration::PERSISTENCE_ARGS => [],
                ArrayConfiguration::WORKFLOW_FACTORY => $factory
            ]);

            $workflowManager->setConfiguration($configuration);

            $this->workflowManager = $workflowManager;
            $this->flagInitWorkflowManager = true;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @When Progress workflow with alias :entryAlias. Workflow name: :workflowName. Initial action id: :initialAction
     *
     * @param string $entryAlias
     * @param string $workflowName
     * @param integer $initialAction
     *
     * @throws \RuntimeException
     */
    public function initializeWorkflowEntry($entryAlias, $workflowName, $initialAction)
    {
        $entryAlias = (string)$entryAlias;
        if (array_key_exists($entryAlias, $this->entryAliasToEntryId)) {
            $errMsg = sprintf('Alias %s already exists', $entryAlias);
            throw new \RuntimeException($errMsg);
        }

        $workflowManager = $this->getWorkflowManager();

        try {
            $entryId = $workflowManager->initialize($workflowName, $initialAction, $this->transientVars);
            $this->entryAliasToEntryId[$entryAlias] = $entryId;
        } catch (\Exception $e) {
            $this->lastException = $e;
            //throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @When Call action with id=:actionId for workflow process with alias :entryAlias
     *
     * @param $entryAlias
     * @param $actionId
     *
     * @throws \RuntimeException
     */
    public function callActionWithIdForWorkflowProcessWithAlias($entryAlias, $actionId)
    {
        $entryAlias = (string)$entryAlias;
        if (!array_key_exists($entryAlias, $this->entryAliasToEntryId)) {
            $errMsg = sprintf('Alias %s not exists', $entryAlias);
            throw new \RuntimeException($errMsg);
        }
        $entryId = $this->entryAliasToEntryId[$entryAlias];

        $workflowManager = $this->getWorkflowManager();

        try {
            $workflowManager->doAction($entryId, $actionId, $this->transientVars);
        } catch (\Exception $e) {
            $this->lastException = $e;
            //throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @Then Last action was the result of class exception :exceptionClassName. The massage of exception: :exceptionText
     *
     * @param $exceptionClassName
     * @param $exceptionText
     *
     * @throws \RuntimeException
     */
    public function lastActionWasTheResultOfClassExceptionTheMassageOfException($exceptionClassName, $exceptionText)
    {
        if (!$this->lastException instanceof $exceptionClassName) {
            $errMsg = sprintf('Last action was the result of class exception "%s"', $exceptionClassName);
            throw new \RuntimeException($errMsg);
        }

        if ($exceptionText !== $this->lastException->getMessage()) {
            $errMsg = sprintf('Invalid text exception. Expected %s', $exceptionText);
            throw new \RuntimeException($errMsg);
        }
    }

    /**
     * @return BasicWorkflow
     *
     * @throws \RuntimeException
     */
    protected function getWorkflowManager()
    {
        if (null === $this->workflowManager) {
            $errMsg = 'The  WorkflowManager has not been established';
            throw new \RuntimeException($errMsg);
        }

        return $this->workflowManager;
    }

    /**
     * @param string $entryAlias
     * @return integer
     *
     * @throws \RuntimeException
     */
    protected function getEntryIdByAlias($entryAlias)
    {
        if (!array_key_exists($entryAlias, $this->entryAliasToEntryId)) {
            $errMsg = 'The  WorkflowManager has not been established';
            throw new \RuntimeException($errMsg);
        }

        return $this->entryAliasToEntryId[$entryAlias];
    }

    /**
     * @Then Process of workflow with the alias :entryAlias has the below steps:
     *
     * @param string          $entryAlias
     * @param TableNode $steps
     *
     * @throws \RuntimeException
     */
    public function validateCurrentSteps($entryAlias, TableNode $steps)
    {
        $entryId = $this->getEntryIdByAlias($entryAlias);

        $currentSteps = $this->getWorkflowManager()->getConfiguration()->getWorkflowStore()->findCurrentSteps($entryId);
        $actualCurrentSteps = [];
        foreach ($currentSteps as $currentStep) {
            $actualCurrentSteps[(integer)$currentStep->getStepId()] = $currentStep;
        }

        $stepsColumn = $steps->getColumn(0);
        if (count($stepsColumn) < 2 || 'stepId' !== array_shift($stepsColumn)) {
            $errMsg = 'Incorrect step id list';
            throw new \RuntimeException($errMsg);
        }

        foreach ($stepsColumn as $currentStepFromColumn) {
            $currentStepFromColumn = (integer)$currentStepFromColumn;
            if (!array_key_exists($currentStepFromColumn, $actualCurrentSteps)) {
                $errMsg = sprintf('Step not found %s', $currentStepFromColumn);
                throw new \RuntimeException($errMsg);
            }
        }

        if (count($actualCurrentSteps) !== count($stepsColumn)) {
            throw new \RuntimeException('there are extra currentSteps ');
        }
    }

    /**
     * @Then Exceptions are missing
     *
     * @throws RuntimeException
     */
    public function exceptionsAreMissing()
    {
        if (null !== $this->lastException) {
            throw new \RuntimeException($this->lastException->getMessage(), $this->lastException->getCode(), $this->lastException);
        }
    }

    /**
     * @Then There is the valuable :name with value :value in Transient Vars
     *
     * @param string $name
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function thereIsTheValuableWithValueInTransientVars($name, $value)
    {
        if (!$this->transientVars->offsetExists($name)) {
            $errMsg = sprintf('Property %s not found in transient vars', $name);
            throw new \RuntimeException($errMsg);
        }
        $actual = $this->transientVars[$name];

        if ($value !== $actual) {
            $errMsg = sprintf('Property %s. Expected value %s. Actual %s', $name, $value, $actual);
            throw new \RuntimeException($errMsg);
        }
    }
}
