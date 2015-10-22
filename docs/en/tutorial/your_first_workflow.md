#OSWorkflow - Your first workflow

First, let us define the workflow. You can name this workflow whatever you want. Workflow definitions are specified in an XML file, one workflow per file. Let us start by creating a file called 'myworkflow.xml". The boilerplate data for this file is as follows:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC 
  "-//OpenSymphony Group//DTD OSWorkflow 2.8//EN"
  "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
  <initial-actions>
    ...
  </initial-actions>
  <steps>
    ...
  </steps>
</workflow>
```

We have the standard XML header specified first. Note that OSWorkflow will validate all XML files to the specified DTD, so the workflow definition has to be valid. You can edit it using most XML tools and errors will be highlighted appropriately.

### Steps and actions

Next we specify initial-actions and steps. The first important concept to understand is that of steps and actions in OSWorkflow. A step is simply a workflow position. As a simple workflow progresses, it moves from one step to another (or even stays in the same step sometimes). As an example, steps names for a document management system would be along the lines of 'First Draft', 'Edit Stage', and 'At publisher".

Actions specify the transitions that can take place within a particular step. An action can often result in a change of step. Examples of actions in our document management system would be 'start first draft' and 'complete first draft' in the 'First Draft' step we specified above.

Put simply, a step is 'where it is', and an action is 'where it can go'.

Initial actions are a special type of action that are used to 'kick off' the workflow. At the very beginning of a workflow, we have no state, and are not in any step. The user must take some action to start off the process, and this set of possible actions to start the workflow is specified in <initial-actions>.

For our example, let us assume that we only have one initial-action, which is simply, 'Start Workflow'. Add the following action definition inside of <initial-actions>:

```xml
<action id="1" name="Start Workflow">
  <results>
    <unconditional-result old-status="Finished" status="Queued" step="1"/>
  </results>
</action>
```

This action is the simplest possible type of action. It simply specifies the step we move to, as well as what values to set the status to.

### Workflow status

A workflow status is a string that describes the status of a workflow within a particular step. In our document management system, our 'First Draft' step might have two disinct statuses, 'Underway', and 'Queued' that it cares about.

We use 'Queued' to denote that the item has been queued in the 'First Draft'. Let's say someone has requested that a particular document be written, but no author has been assigned. So the document is now currently 'Queued' in the 'First Draft' step. The 'Underway' status would be used to denote that an author has picked the document from the queue and perhaps locked it, denoting that he is now working on the first draft.

### The first step

Let us examine how that first step would be defined in our <steps> element. We know we have two actions. The first of these actions (start first draft) keeps us in the same step, but changes the status to 'Underway'. The second action moves us to the next step in the workflow, which in this case is a 'finished' step, for the sake of simplicity. So we now add the following inside our <steps> element:

```xml
<step id="1" name="First Draft">
  <actions>
    <action id="1" name="Start First Draft">
      <results>
        <unconditional-result old-status="Finished" status="Underway" step="1"/>
      </results>
    </action>
    <action id="2" name="Finish First Draft">
      <results>
        <unconditional-result old-status="Finished" status="Queued" step="2"/>
      </results>
    </action>
  </actions>
</step>
<step id="2" name="finished" />
```

Above we see the two actions defined. The old-status attribute is used to denote what should be entered in the history table for the current state to denote that it has been completed. In almost all cases, this will be 'Finished'.

The actions as specified above are of limited use. For example, it is possible for a user to call action 2 without first having called action 1. Clearly, it should not be possible to finish a draft that has yet to be started. Similarly, it is possible to also start the first draft multiple times, which also makes no sense. Finally, we also have nothing in place to stop a second user cannot from finishing first user's draft. This is also something we'd like to avoid.

Let us tackle these problems one at a time. First, we'd like to specify that a caller can only start a first draft when the workflow is in the 'Queued' state. This would stop users from being able to start the first draft multiple times. To do so, we specify a restriction on the action. The restriction consists of a condition.

### Conditions

OSWorkflow has a number of useful built-in conditions that can be used. The relevant condition in this case 'StatusCondition'. Conditions can also accept arguments, which are usually specified in the javadocs for a particular condition (if it is a condition implemented as a java class).

A condition, like functions and other base constructs, can be implemented in a variety of ways, including beanshell scripts, or java classes that implement the Condition interface.

In this case for example, we use the status condition class. The status condition takes an argument called 'status' which specifies the status to check in order for the condition to pass. This idea becomes much clearer if we examine the XML required to specify this condition:

```xml
<action id="1" name="Start First Draft">
  <restrict-to>
    <conditions>
      <condition type="class">
        <arg name="class.name">
          com.opensymphony.workflow.util.StatusCondition
        </arg>
        <arg name="status">Queued</arg>
      </condition>
    </conditions>
  </restrict-to>
  <results>
    <unconditional-result old-status="Finished" status="Underway" step="1"/>
  </results>
</action>
```

Hopefully the idea of conditions is clearer now. The above condition ensures that action 1 can only be invoked if the current status is 'Queued', which it only ever is right after our initial action has been called.

### Functions

Next, we'd like to specify that when a user starts the first draft, they become the 'owner'. To do this, we need a couple of things:

1. A function that places a 'caller' variable in the current context.
2. Setting the 'owner' attribute of the result to that 'caller' variable.

Functions are a powerful feature of OSWorkflow. A function is basically a unit of work that can be performed during a workflow transition, that does not affect the workflow itself. For example, you could have a 'SendEmail' function that is responsible for sending out an email notification when a particular transition takes place.

Functions can also add variables to the current context. A variable is a named object that is made available to the workflow and can be referenced later on by other functions or scripts.

OSWorkflow comes with a number of useful built-in functions. One of these functions is the 'Caller' function. This function looks up the current user invoking the workflow, and exposes a named variable called 'caller' that is the string value of the calling user.

Since we'd like to keep track of who started our first draft, we would use this function by modifying our action as follows:

```xml
<action id="1" name="Start First Draft">
  <pre-functions>
    <function type="class">
      <arg name="class.name">com.opensymphony.workflow.util.Caller</arg>
    </function>
  </pre-functions>
  <results>
    <unconditional-result old-status="Finished" status="Underway" 
                                       step="1" owner="${caller}"/>
  </results>
</action>
```

### Putting it all together

Putting the ideas above together, we now have the following definition for action 1:

```xml
<action id="1" name="Start First Draft">
  <restrict-to>
    <conditions>
      <condition type="class">
        <arg name="class.name">
                com.opensymphony.workflow.util.StatusCondition
        </arg>
        <arg name="status">Queued</arg>
      </condition>
    </conditions>
  </restrict-to>
  <pre-functions>
    <function type="class">
      <arg name="class.name">
              com.opensymphony.workflow.util.Caller
      </arg>
    </function>
  </pre-functions>
  <results>
    <unconditional-result old-status="Finished" status="Underway" 
                                       step="1"  owner="${caller}"/>
  </results>
</action>
```

We use the same ideas when defining action 2:

```xml
<action id="2" name="Finish First Draft">
  <restrict-to>
    <conditions type="AND">
      <condition type="class">
        <arg 
        name="class.name">com.opensymphony.workflow.util.StatusCondition
        </arg>
        <arg name="status">Underway</arg>
      </condition>
      <condition type="class">
        <arg name="class.name">
              com.opensymphony.workflow.util.AllowOwnerOnlyCondition
       </arg>
      </condition>
    </conditions>
  </restrict-to>
  <results>
    <unconditional-result old-status="Finished" status="Queued" step="2"/>
  </results>
</action>
```

Here we specify a new condition, the 'allow owner only' condition. This ensures that only the user that started the first draft can finish it (which we specified in the previous result's owner attribute). The status condition likewise ensures that the 'finish first draft' action can only be performed when the status is 'Underway', which happens only after a user has started the first draft.

Putting it all together, we have our complete workflow definition below:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC 
                 "-//OpenSymphony Group//DTD OSWorkflow 2.8//EN"
                 "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
  <initial-actions>
    <action id="1" name="Start Workflow">
      <results>
        <unconditional-result old-status="Finished" status="Queued" step="1"/>
      </results>
    </action>
  </initial-actions>
  <steps>
    <step id="1" name="First Draft">
      <actions>
        <action id="1" name="Start First Draft">
          <restrict-to>
            <conditions>
              <condition type="class">
                <arg name="class.name">
                   com.opensymphony.workflow.util.StatusCondition
                </arg>
                <arg name="status">Queued</arg>
              </condition>
            </conditions>
          </restrict-to>
          <pre-functions>
            <function type="class">
              <arg name="class.name">
                 com.opensymphony.workflow.util.Caller
              </arg>
            </function>
          </pre-functions>
          <results>
            <unconditional-result old-status="Finished" status="Underway" 
                                           step="1"  owner="${caller}"/>
          </results>
        </action>
        <action id="2" name="Finish First Draft">
          <restrict-to>
            <conditions type="AND">
              <condition type="class">
                <arg name="class.name">
                    com.opensymphony.workflow.util.StatusCondition
                </arg>
                <arg name="status">Underway</arg>
              </condition>
              <condition type="class">
                <arg name="class.name">
                  com.opensymphony.workflow.util.AllowOwnerOnlyCondition
                </arg>
              </condition>
            </conditions>
          </restrict-to>
          <results>
            <unconditional-result old-status="Finished" status="Queued" step="2"/>
          </results>
        </action>
      </actions>
    </step>
    <step id="2" name="finished" />
  </steps>
</workflow>
```

Now that the workflow definition is complete, it's time to test it and verify its behaviour.

Go to [Testing your workflow](testing_your_workflow.md).
