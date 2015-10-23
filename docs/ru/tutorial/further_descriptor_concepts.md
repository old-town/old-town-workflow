# OSWorkflow - Further descriptor concepts
        
## Defining functions and conditions</a></h3>

You might have noticed that so far, all the conditions and functions are of type 'class'. Functions and conditions of this type take in one argument, of the name 'class.name', which specifies a fully qualified class name that implementes the appropriate interface (FunctionProvider and Condition, respectively).

There are a number of other built-in types, which include beanshell, stateless session beans, as well as functions placed in JNDI. We will use the beanshell type below.

## Property sets

At any point in the workflow, you will likely want to persist small pieces of data. This is made possible in OSWorkflow by the use of the OpenSymphony PropertySet library. A PropertySet is basically a persistent type-safe Map. You can add items to the propertyset (one is created per workflow) and later on retrieve them. The propertyset is not emptied or deleted unless you explicitly do so yourself. Every function and condition has access to this propertyset, as well as any inline scripts, where it is added to the script context with the name 'propertySet'. So, to illustrate an inline script accessing the property set, let's add the following to our 'Start First Draft' actions' pre-functions.

```xml
<function type="beanshell">
  <arg name="script">propertySet.setString("foo", "bar")</arg>
</function>
```

We've now added a persistent property called 'foo', with the value of 'bar'. At any point in the workflow from now on, we will be able to retrieve this value.

## Transient variables

As well as the propertyset variable, the other important variable made available to workflow scripts, functions, and conditions is the 'transientVars' map. This map is simply a transient map that contains context specific information for the current workflow invocation. It includes the current workflow instance being manipulated, as well as the current workflow store and the workflow descriptor being used as well as other relevant values. You can see a list of all the available keys in the javadocs for FunctionProvider and Condition.

Remember the inputs parameter earlier, which we always passed in as null? Well, if we hadn't passed in a null, the contents of the map we pass in will have been added to the transient map.

## Inputs

Every invocation of a workflow action takes an optional input map. This map can contain any arbitrary data that you might want to make available to your functions or conditions. It is not persisted anywhere, and is simply a data-passing mechanism.

## Validators

In order to allow for the workflow to validate inputs, we have the concept of Validators. A Validator is very similarly implemented as a function or condition (ie, it can be a class or script or EJB). For the purposes of this tutorial, we'll define a validator that checks that the input to 'finish first draft' includes a 'working.title' input that is not greater than 30 characters. Our validator will look something like this:


```java
package com.mycompany.validators;

public class TitleValidator implements Validator
{
  public void validate(Map transientVars, Map args, PropertySet ps) 
        throws InvalidInputException, WorkflowException
  {
    String title = (String)transientVars.get("working.title"); 
    if(title == null)
      throw new InvalidInputException("Missing working.title");
    if(title.length() > 30)
      throw new InvalidInputException("Working title too long");
  }
}
```

Next, we register the validator in our workflow definition, by adding a validators element after our restrict-to element:

```xml
<validators>
  <validator type="class">
    <arg name="class.name">
      com.mycompany.validators.TitleValidator
    </arg>
  </validator>
</validators>
```

So now, when we try to execute action 2, the validator above will be called to validate the input we had specified. So in our test case, if we now add:


```java
Map inputs = new HashMap();
inputs.put("working.title", 
  "the quick brown fox jumped over the lazy dog," +
  " thus making this a very long title");
workflow.doAction(workflowId, 2, inputs);
```

We will get an InvalidInputException thrown, and the action will not be executed. Shortening the title will result in a successful execution of the action.

Now that we have covered inputs and validators, let us move on to registers.

## Registers

A register is a global variable in a workflow. Similar to a propertyset, it can be accessed anywhere in the workflow, for as long as it is active. The difference however is that a register is not a persistent value, it is a calculated value that is created or looked up anew with every workflow invocation.

How is this useful? Well, in our document management system, it would be useful to have a 'document' register that allows functions, conditions, and scripts to access the document being edited.

Registers are placed in the transientVars map, and so can be accessed from almost anywhere.

Defining a register is very similar to defining a function or condition, with one important difference. Since a register is not invocation-specific (ie, it doesn't care about the current step, or what the inputs are; all it does is expose something), it does not have access to transientVars.

Registers must implement the Register interface, and are specified at the top of the workflow definition, before initial actions.

For our example, we'll specify one of the built-in registers, LogRegister. This register simply adds a 'log' variable that allows you to log messages using Jakarta's commons-logging. The advantage of using it is that it will also add the instance ID to every log message.

```xml
<registers>
  <register type="class" variable-name="log">
    <arg name="class.name">
      com.opensymphony.workflow.util.LogRegister
    </arg>
    <arg name="addInstanceId">true</arg>
    <arg name="Category">workflow</arg>
  </register>
</registers>
```

Now we have a 'log' variable available, we can use it in an inline script by adding another pre-function:

```xml
<function type="beanshell">
  <arg name="script">transientVars.get("log").info("executing action 2")</arg>
</function>
```

The logging output will now contain the workflow instance ID.

## Conclusion

This tutorial has hopefully illustrated the some of the major ideas in OSWorkflow. You should feel comfortable enough now with the API and descriptor syntax to explore further on your own. There are many more advanced features that are not mention here, like splits, joins, nested conditions, auto steps, and others. Feel free to browse the manual to get a stronger grasp on what is available.

If you do get stuck at any point, then please feel free to ask on OldTown Workflow mailing list!
