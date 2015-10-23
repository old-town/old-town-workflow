# OSWorkflow - Creating a new workflow

* Back to [Interface choices](interface_choices.md)
* Forward to [Executing actions](executing_actions.md)

This is a very brief guide on how you can create a new workflow instance using the OSWorkflow Java APIs. First, the workflow definition file (in XML) must be created and defined using the [Loading Workflow Definitions](loading_workflow_definitions.md). Then your code must know what the <b>initialStep</b> value should be for initializing an instance. Before you can initialize a workflow you must <b>create</b> it so that you have an ID that can be referenced from now on in the API. The following code example illustrates this:

```java
Workflow wf = new BasicWorkflow(username);
HashMap inputs = new HashMap();
inputs.put("docTitle", request.getParameter("title"));
wf.initialize("workflowName", 1, inputs);
```

Note that usually, you would use a more appropriate Workflow implementation rather than BasicWorkflow. For example, EJBWorkflow or OfbizWorkflow. If you want to use a Workflow store that does not have a custom workflow context implementation (for example, JDBC, or Hibernate), then use BasicWorkflow. Contributions of WorkflowContexts for various stores are more than welcome!
