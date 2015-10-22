OSWorkflow - Workflow Definition

* Back to [3. Understanding OSWorkflow](understanding_OSWorkflow.md)
* Forward to [3.2 Workflow Concepts](workflow_concepts.md)

At the heart of OSWorkflow is the workflow definition descriptor. The descriptor is an XML file (although it doesn't have to be, but XML is the format supported out of the box).

This descriptor describes all the steps, states, transitions, and functionality for a given workflow.

* A workflow consists of multiple steps to represent the flow.
* For the current step, there may be multiple actions. An action may be set to run automatically or be selected to be run programmatically through user interaction.
* Each action has at least one unconditional result and zero or more conditional results.
* If multiple conditional results are specified, the first result for which all conditions are met is executed. If no conditional results are specified, or if no conditions are satisfied, then the unconditional result is executed.
* A result might remain in the current step, reference a new step, reference a split, or reference a join. In all cases, the state of the workflow can also change (example workflow states are Underway, Queued, and Finished).
* If a result causes a split, the result specifies a split attribute which points to a split element that defines the splits.
* A split may have one or more unconditional results, but no conditional results. The unconditional results reference the steps coming from the split.
* A register is a global variable, that is resolved for every workflow invocation and is always available to all functions and conditions.
* A propertyset is a map of persistent data that is available globally.
* A map called 'transientVars' is a map of transient data that is available to all functions and conditions that includes all registers, user inputs, as well as the current workflow context and state. It exists only during the lifetime of a workflow invocation.
