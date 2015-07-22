<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use \OldTown\Workflow\Loader\AbstractDescriptor;
use \Behat\Behat\Hook\Scope\AfterStepScope;

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
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given Create WorkflowDescriptor
     *
     *
     * @return AbstractDescriptor
     * @throws \RuntimeException
     */
    public function createWorkflowDescriptorByName()
    {
        $descriptor = $this->createDescriptorByName('WorkflowDescriptor');

        return $descriptor;
    }

    /**
     * @Given Add RegisterDescriptor the previous descriptor
     * @throws \RuntimeException
     */
    public function addRegisterDescriptorThePreviousDescriptor()
    {
        $descriptor = $this->createDescriptorByName('RegisterDescriptor');

        return $descriptor;
    }

    /**
     *
     *
     * @param $name
     *
     * @return AbstractDescriptor
     * @throws \RuntimeException
     */
    protected function createDescriptorByName($name)
    {
        $ns = $this->getWorkflowDescriptorNamespace();
        $class = "{$ns}\\{$name}";

        if (!class_exists($class)) {
            $errMsg = "Class not found {$class}";
            throw new \RuntimeException($errMsg);
        }

        $descriptor = new $class;

        if (!$descriptor instanceof AbstractDescriptor) {
            $errMsg = 'Descriptor not instance of AbstractDescriptor';
            throw new \RuntimeException($errMsg);
        }

        return $descriptor;
    }

    /**
     *
     *
     * @return string
     */
    public function getWorkflowDescriptorNamespace()
    {
        return $this->workflowDescriptorNamespace;
    }

    /** @AfterStep
     * @param  $event
     */
    public function after(AfterStepScope $event)
    {

        die('dede');
    }
}
