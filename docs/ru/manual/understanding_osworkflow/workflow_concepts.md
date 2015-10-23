# OSWorkflow - Workflow Concepts

* Back to [Workflow Definition](workflow_definition.md)
* Forward to [Common and Global Actions](common_and_global_actions.md)

OSWorkflow is very unique compared to other workflow engines one might be familiar with. In order to completely grasp OSWorkflow and properly harness the features available, it is important that one understand the core concepts that form the foundation for OSWorkflow.

## Steps, Status, and Actions

Any particular __workflow instance__ can have one or more __current steps__ at any given moment. Every current step has a __status value__ associated to it. Status values of the current steps constitute __workflow status__ for that workflow instance. *The actual status values are entirely up to the application developer and/or project manager*. A status value can be, for example "Underway", or "Queued".

For the workflow to progress, a __transition__ must take place in the finite state machine that represents a workflow instance. Once a step is completed it can not be current anymore. Usually a new current step is created immediately thereafter, which keeps the workflow going. The final status value of the completed step is set by the *old-status* attribute. It happens just before the transition to another step. __Old-status__ must already be defined when a new transition takes place in the workflow. __It can be any value you please, but "Finished" usually works fine for most applications__.

__Transition__ itself is a result of an __action__. A step may have many actions connected to it. Which particular action will be launched is determined by the end user, external event or automatically by a trigger. Depending on the action accomplished, a certain transition takes place. Actions can be restricted to particular groups and users or current state of the workflow. Each action must have one __unconditional result__ (default) and zero or more __conditional results__.

So, to summarize, a workflow consists of a number of Steps. A step has a current status (for example, Queued, Underway, or Finished). A step has a number of Actions that can be performed in it. An action has Conditions under which it is available, as well as Functions that are executed. Actions have results that change the state and current step of the workflow.

## Results, Joins, and Splits

### Unconditional Result

For every action, it is required that there exist one result, called the unconditional-result. A result is nothing more than a series of directives that tell OSWorkflow what the next task to do is. This involves making a transition from one state to the next state(s) in the state machine that makes up a given workflow.

### Conditional Results

A conditional result is an extension of an unconditional result. It is identical, except for the fact that it requires one or more additional sub-elements: *condition*. The first conditional result that evaluates to true (using the types *AND* or *OR*) will dictate the transition that takes place due to the result of any given action taken by the user. Additional information regarding conditions can be found below.

### There are three different results (conditional or unconditional) that can occur:

* A new single step/status combo
* A split in to two or more step/status combos
* A join that joins together this transition as well as others to a new single step/status combo

Depending on what kind of behavior you are looking for, your XML workflow descriptor will look different. Please read the DTD (which provides documentation as well) in [Appendix A](http://www.opensymphony.com/osworkflow/workflow_2_6.dtd) for more information. __One caveat: currently a split or a join cannot result in an immediate split or join again__.

#### A single step/status result can be specified simply by:


```xml
<unconditional-result old-status="Finished" step="2" 
                      status="Underway" owner="${someOwner}"/>
```

If the status is not Queued, then a third requirement is the owner of the new step. Besides specifying information about the next state, results also can specify __validators__ and __post-functions__. These will be discussed below.

In certain cases the result of an action does not require a transition to another step. Such a result may be specified by setting the step value to -1.  For example, we can change the above example to remain in the current step (or steps) as follows:


```xml
<unconditional-result old-status="Finished" step="-1" 
                      status="Underway" owner="${someOwner}"/>
```

#### Splitting from one state to multiple states can be achieved by:


```xml
<unconditional-result split="1"/>
...
<splits>
  <split id="1">
    <unconditional-result old-status="Finished" step="2" 
                          status="Underway" owner="${someOwner}"/>
    <unconditional-result old-status="Finished" step="2" 
                          status="Underway" owner="${someOtherOwner}"/>
  </split>
</splits>
```

#### Joins are the most complex cases. A typical join might look like:


```xml
<!-- <span class="code-keyword">for step id 6 ->
<unconditional-result join="1"/>
...
<!- <span class="code-keyword">for step id 8 ->
<unconditional-result join="1"/>
...
<joins>
  <join id="1">
    <join id="1">
    <conditions type="AND">
      <condition type="beanshell">
        <arg name="script">
          "Finished".equals(jn.getStep(6).getStatus() 
          &amp;&amp; "Finished".equals(jn.getStep(8).getStatus())
        </arg>
      </condition>
    </conditions>
  </join>
  <unconditional-result old-status="Finished" status="Underway" 
                                 owner="test" step="2"/>
  </join>
</joins>
```

The above might seem somewhat cryptic, but the main thing to notice is that the *condition element* uses a special variable *"jn"* that can be used to make up expressions that determine when the join actually occurs. Essentially, this expression statement says *"proceed with the join when the steps with IDs 6 and 8 that are transitioning to this join have a status of Finished".*

## External Functions

OSWorkflow defines a standard way for external business logic and services to be defined and executed. This is accomplished by using "functions". A function usually encapsulates functionality that is external to the workflow instance itself, perhaps related to updating an external entity or system with workflow information, or notifying an external system regarding a change in workflow status.

__There are two types of functions: pre and post step functions.__

Pre functions are functions that are executed before the workflow makes a particular transition. An example is a pre function that sets up the name of the caller to use as the result for the state change that is about to take place. Another example of a pre-function is a function that updates the most recent caller of an action. Both of these are provided as standard utility functions that are very useful for practical workflows.

Post functions have the same range of applicability as pre functions, except that they are executed after the state change has taken place. An example of a post function is one that sends out an email to interested parties that the workflow has had a particular action performed on it. For example, when a document in the 'research' step has a 'markReadyForReview' action taken, the reviewers group is emailed.

There are many reasons for including pre and post functions. One is that if the user were to click the "done" button twice and to send out two "execute action" calls, and that action had a pre function that took a long time to finish, then it is possible the long function could get called multiple times, because the transition hasn't been made yet, and OSWorkflow thinks the second call to perform the action is valid. So changing that function to be a post function is what has to happen. __Generally pre functions are for simple, quick executions, and post are where the "meat" goes.__

Functions can be specified in two separate locations; steps and actions.

Usually, a pre or post function is specified in an action. The general case is that along with transitioning the workflow, a functions is used to 'do something', whether it be notifying a third party, sending an email, or simply setting variables for future use. The following diagram will help illustrate action level functions:

![Action Functions](actionfunctions.png)

In the case of pre and post functions being specified on steps, the usage is slightly different. Pre-functions specified on a step will be executed __before__ the workflow is transitioned to that step. Note that these functions will be applied indiscriminantly to ALL transitions to the step, even those that originate in the step itself (for example, moving from Queued to Underway within the same step will cause the invocation of any step pre-functions specified).

Similarly, step post-functions will be called prior to the workflow transitioning __out__ of the step, even if it's to change state and remain within the step.

The following diagram illustrates the invocation order. Note that the action box is abbreviated and could well contain pre and post functions of its own.

![Step Functions](stepfunction.png)

You can find more information on [Functions](functions.md).

## Trigger Functions

Trigger functions are just like any other function, except that they aren't associated with only one action. They are also identified with a unique ID that is used at a later time (when a trigger is fired) to be executed by the Quartz job scheduler (or any other job scheduler). These functions usually run under the context of a system user and not a regular user working in the workflow. Trigger functions are invoked by using OldTown Workflow API from an outside source, such as a job scheduler like Quartz.

You can find more information on [Trigger functions](trigger_functions.md).

## Validators

A validator is nothing more than some code that validates the input that can be paired with an action. If the input is deemed to be valid, according to the validator, the action will be executed. If the input is invalid, the __InvalidInputException__ will be thrown back to the calling client - usually a JSP or servlet.

Validators follow many of the same rules as Functions. You can find out more about [Validators](validators.md).

## Registers

A register is a helper function that returns an object that can be used in Functions for easy access to common objects, especially entities that revolve around the workflow. The object being registered can be any kind of object. Typical examples of objects being registered are: Document, Metadata, Issue, and Task. This is strictly for convenience and does not add any extra benefit to OSWorkflow besides making the developer's life much simpler. Here is an example of a register:

```xml
<registers>
	<register name="doc" class="com.acme.DocumentRegister"/>
</registers>
...
<results>
	<result condition="doc.priority == 1" step="1" status="Underway" 
                  owner="${someManager}"/>
	<unconditional-result step="1" status="Queued"/>
</results>
...
```

## Conditions

Conditions, just like validators, registers, and functions, can be implemented in a variety of languages and technologies. Conditions can be grouped together using *AND* or *OR* logic. Any other kind of complex login must be implemented by the workflow developer. Conditions usually associated with conditional results, where a result is executed based on the conditions imposed on it being satisfied.

Conditions are very similar to functions except that they return *boolean* instead of *void*. You can find out more about [Conditions](conditions.md).

## Variable Interpolation

In all functions, conditions, validators, and registers it is possible to provide a set of *args* to the code of choice. These args are translated to the *args Map* that is discussed in further detail later on. Likewise the *status, old-status, and owner elements* in the workflow descriptor are also all parsed for variables to be dynamically converted. A variable is identified when it looks like *${foo}*. OSWorkflow recognizes this form and first looks in the *transientVars* for the key foo. If the key does not exist as a transient variable, then then  *propertySet* is searched. If the propertyset does not contain the specified key either, then the entire variable is converted to an empty String.

One thing of particular importance is that in the case of *args*, if the variable is the only argument, the argument will not be of type String, but instead whatever the variable type is. However, if the arg is a mix of characters and variables, the entire argument is converted to String no matter what. That means the two arguments below are very different in that foo is a Date object and bar is a String:

```xml
<arg name="foo">${someDate}</arg>
<arg name="bar"> ${someDate} </arg> <!-- note the extra spaces -->
```

## Permissions and Restrictions

__Permissions__ can be assigned to users and/or groups based on the state of the workflow instance. These permissions are unrelated to the functionality of the workflow engine, but they are useful to have for applications that implement OSWorkflow. For example, a document management system might have the permission name "file-write-permission" enabled for a particular group only during the "Document Edit" stage of the workflow. That way your application can use the API to determine if files can be modified or not. This is useful as there could be a number of states within the workflow where the "file-write-permission" is applicable, so instead of checking for specific steps or conditions, the check can simply be made for a particular permission.

Permissions and actions both use the concept of __restrictions__. *A restriction is nothing more than one or more conditions embedded inside a restrict-to element.*

## Auto actions

Sometimes it is desirable to have an action performed automatically, based on specific conditions. This is useful for example when trying to add automation to a workflow. In order to achieve this, an attribute of *auto="true"* will have to be added to the specific action. The workflow engine will then evaluate the conditions and restrictions on the action, and if they are matched and the workflow *could* perform the action, then it automatically does so. The auto action executes with the current caller, so the permissions checks and so on are performed against the user who called the action that initiated the auto action.

## Integrating with Abstract Entities

Because OSWorkflow is not an out-of-the-box solution, some development work is required to make your project work correctly with OSWorkflow. It is recommended that your core entity, such as "Document" or "Order", be given a new attribute: __workflowId__. That way when a new Document or Order is created, it can be associated with a workflow instance also. Then your code can look up that workflow instance and retrieve workflow information and/or issue workflow actions via OldTown Workflow API.

## Workflow Instance State (Available since OSWorkflow 2.6)

Sometimes it is helpful to specify a state to the workflow instance as a whole, independent to its progression or its steps. OSWorkflow offers a number of such "meta-states" that a workflow instance can be in. These are *CREATED*, *ACTIVATED*, *SUSPENDED*, *KILLED*, and *COMPLETED*. When a workflow instance is first created, it is in the *CREATED* state. Then as soon as an action is performed on it, it moves automatically to the *ACTIVATED* state. If the caller does not explicitly alters the instance state, the workflow will remain in this state until it is unambigiously completed. This is defined to be the case when the workflow cannot possibly perform any further actions. In this case, the workflow will automatically move to the *COMPLETED* state.

However, while the workflow is in the *ACTIVATED* state, a caller can termined or suspend the workflow (set the state to *KILLED* or *SUSPENDED*). A terminated workflow will not be able to progress any further, and will remain in the state it was in at the time of termination forever. A workflow that has been suspended will be 'frozen' and no actions can be performed on it, until it is explicitly returned back to the *ACTIVATED* state.

* Back to [Workflow Definition](workflow_definition.md)
* Forward to [Functions](functions.md)
