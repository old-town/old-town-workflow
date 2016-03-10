<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Workflow\Config\ConfigurationInterface;
use OldTown\Workflow\Loader\WorkflowDescriptor;

/**
 * Interface WorkflowInternalInterface
 *
 * @package OldTown\Workflow
 */
interface WorkflowInternalInterface extends WorkflowInterface
{
    /**
     * Get the available actions for the specified workflow instance.
     * @param integer $id The workflow instance id.
     * @param array $inputs The inputs map to pass on to conditions
     *
     * @return []
     *
     */
    public function getAvailableActions($id, array $inputs = []);

    /**
     * Set the configuration for this workflow.
     * If not set, then the workflow will use the default configuration static instance.
     * @param ConfigurationInterface $configuration a workflow configuration
     * @return $this
     */
    public function setConfiguration(ConfigurationInterface $configuration);

    /**
     * Get all available workflow names.
     *
     * @return String[]
     */
    public function getWorkflowNames();

    /**
     * Determine if a particular workflow can be initialized.
     * @param string $workflowName The workflow name to check.
     * @param integer $initialAction The potential initial action.
     * @param array $inputs The inputs to check.
     * @return boolean true if the workflow can be initialized, false otherwise.
     */
    //public function canInitialize($workflowName, $initialAction, array $inputs = []);

    /**
     * Remove the specified workflow descriptor.
     * @param string $workflowName The workflow name of the workflow to remove.
     */
    public function removeWorkflowDescriptor($workflowName);

    /**
     * Add a new workflow descriptor
     * @param string $workflowName The workflow name of the workflow to add
     * @param WorkflowDescriptor $descriptor The workflow descriptor to add
     * @param boolean $replace true, if an existing descriptor should be overwritten
     * @return boolean true if the workflow was added, fales otherwise
     */
    public function saveWorkflowDescriptor($workflowName, WorkflowDescriptor $descriptor, $replace);
}
