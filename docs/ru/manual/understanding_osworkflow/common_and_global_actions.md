# OSWorkflow - Common and Global Actions

* [Workflow Concepts](workflow_concepts.md)
* [Functions](functions.md)

Two convenient features that can help reduce code duplication when defining workflow descriptors are common actions, and global actions.

The basic idea is simple. Both of these types of actions can be defined at the top of the workflow descriptor, after the initial-actions element.

Common actions are useful in cases where at various points of the workflow, a particular action needs to be available. For example, this could be a 'send email' action. The action would likely have some pre-functions, perhaps a validator, perhaps even an inline script. All in all it might be 7-8 lines of xml. Since this action is needed in multiple places, the code would have to be duplicated wherever it's needed.

Defining it as a common action avoids this need for duplication. The action would be defined once as a common action. Then any steps that need to have that action available would simply reference it like this:

```xml
<common-action id="100" />
```

Global actions are slightly different. They are defined the same way (in a global-actions element after initial-actions), but instead of having to be explicitly referenced by a particular step, they are always available to ALL steps. An example of a global action for example could be 'terminate workflow', where at any stage, it is possible to end the workflow (for example, by setting its state to KILLED), and perhaps log it and send an email (defined as functions in the global action).

In both cases, all global and common actions should have unique ID's that do not clash with any other action ID's in the workflow descriptor.

Common and Global actions may not require a transition to another step. In this case, the result of the action may specify the next step as 0 to signify that no transition is required, as with the following example.

```xml
<unconditional-result old-status="Finished" step="0" 
	status="Underway" owner="${someOwner}"/>
```
