# OSWorkflow - Testing your workflow

Now that we have a complete workflow definition, the next step is to verify that it behaves as expected.

The easiest way to do this in a rapid development environment is to write a test case. The test case will invoke the workflow, and by examining the results and the potential errors thrown, we can ensure that our definition is correct.

It is assumed that you are already familiar with JUnit and writing testcases. If not, then go to the JUnit website and go through the documentation there. Writing tests will soon become a very useful addition to your toolbox.

Before we can load in our workflow descriptor and call actions on it, we need to configure OSWorkflow to inform it of what data store to use, as well as the descriptor location and suchlike.

### Configuring osworkflow.xml

The first file that needs to be created is __osworkflow.xml__. Below is a simple example:

```xml
<osworkflow>
  <persistence class="com.opensymphony.workflow.spi.memory.MemoryWorkflowStore"/>
  <factory class="com.opensymphony.workflow.loader.XMLWorkflowFactory">
    <property key="resource" value="workflows.xml" />
  </factory> 
</osworkflow>
```

This example specifies that we should use the memory workflow store. This saves us from the trouble of having to set up a database or some other store that might require a lot of configuration and initialising. Of course, the memory store is only really useful for testing purposes.

### Workflow factories

The configuration file above also specifies that we should use the XML workflow factory. The workflow factory is responsible for managing workflow descriptors. This includes reading them at a bare minimum, and possibly modifying and writing them. The XML workflow factory has a special property called 'resource' which specifies the file where the workflow name to descriptor XML file can be found. The resource is loaded from the classpath, so for the case above, you will need to create a file called workflows.xml that is in your classpath.

The contents of workflows.xml should be:

```xml
<workflows>
  <workflow name="mytest" type="resource" location="myworkflow.xml"/>
</workflows>
```

So, you need to have the __myworkflow.xml__ file we created earlier alongside workflows.xml, since it will likewise be loaded in as a resources.

At this point we're done with configuration, so first initialize then call our workflow.

### Initialising OSWorkflow

OSWorkflow has a fairly simple invocation model. There is a main entry point through which almost all interaction takes place. This main entry point is the __Workflow__ interface, and implementation-wise, is usually a subclass of __AbstractWorkflow__. Example implementations are EJBWorkflow and SOAPWorkflow. For the sake of simplicity, we will use the simplest form, BasicWorkflow.

First, we create our workflow. This object should usually be stored in a global location and should be reused. Although not recommended, one way of doing so is to make it a static. Creating a new one every time can be potentially expensive. BasicWorkflow's constructor takes in one parameter, the username of the current caller. This might seem odd given the earlier recommendation to reuse it, and the fact that any serious usage will involve multiple users. However, most other workflow implementations have their own mechanism for figuring out the current caller, and so are not created 'for' a particular user up front.

BasicWorkflow provides the ability to pin a workflow to a user for the sake of simplicity and to avoid the hassle of hooking up OSWorkflow to a user lookup mechanism.

So, we create our workflow with a user caller 'testuser':

```java
Workflow workflow = new BasicWorkflow("testuser");
```

The next step is to provide the workflow with a configuration to use. In most cases, it is sufficient to simply pass in a DefaultConfiguration instance, like so:

```java
DefaultConfiguration config = new DefaultConfiguration();
workflow.setConfiguration(config);
```

We now have an initialised and configured workflow session, and can move on to invoking a particular workflow and calling actions on it.

### Starting and progressing a workflow
The first thing to do to start a workflow is to call the initialize method. This method takes in 3 parameters. These are the workflow name (how that'll be handled depends on our workflow factory), the action ID (which initial action we want to call), and inputs to the action. For now, we'll simply pass in null for the inputs as we aren't using any (more on them later though).

```java
long workflowId = workflow.initialize("mytest", 1, null);
```

We now have a workflow instance started. The ID returned is what we will use to specify this workflow for all future interactions. This ID is a parameter to most of the methods in the Workflow interface.

#### Verifying the workflow

Now that we've initialised our workflow instance, let's confirm that it behaves as expected. From our workflow definition, we expect that the current step is 1, and that we should be able to only perform action 1 (start first draft).

```java
Collection currentSteps = workflow.getCurrentSteps(workflowId);
//verify we only have one current step
assertEquals("Unexpected number of current steps", 1, currentSteps.size());
//verify it's step 1
Step currentStep = (Step)currentSteps.iterator().next();
assertEquals("Unexpected current step", 1, currentStep.getStepId());

int[] availableActions = workflow.getAvailableActions(workflowId);
//verify we only have one available action
assertEquals("Unexpected number of available actions", 1, availableActions.length);
//verify it's action 1
assertEquals("Unexpected available action", 1, availableActions[0]);
```

#### Calling actions

Now that we've initialised our workflow and verified that it behaves as expected, let's start the first draft!

```java
workflow.doAction(workflowId, 1, null);
```

We simply call the first action. The conditions we've specified on it will be evaluated, and the workflow transitions to be 'Underway', while remaining in the same step.

Similarly, we can then call the second action now that we've called the first, since the required conditions are met.

After calling the second action, we have no more available actions, and as expected, the getAvailableActions will return an empty array.

Congratulations, you have now written and called your first workflow! The next topic will cover more advanced descriptor elements.

Go to [Further descriptor concepts](further_descriptor_concepts.md)
