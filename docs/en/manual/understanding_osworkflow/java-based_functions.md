# OSWorkflow - Java-based Functions

* [Functions](functions.md)
* Forward to [BeanShell Functions](beanshell_functions.md)

Java-based functions must implement the interface *com.opensymphony.workflow.FunctionProvider*. This interface has a single method, __execute__. This method takes three arguments:

* *The transientVars Map* is the exact Map passed by the client code that called *Workflow.doAction()*. This is useful for functions that behave differently based on user input when the action is finished. It also includes a number of special variables that are helpful in accessing various aspects of the workflow. This includes all the variables configured in Registers (see [3.2 Workflow Concepts](workflow_concepts.md)) as well as the following two special variables: *entry* (com.opensymphony.workflow.spi.WorkflowEntry) and *context* (com.opensymphony.workflow.WorkflowContext).
* *The args Map* is a map that contains all the <arg/> tags embedded in the <function/> tag. These arguments are all of type String and have been parsed for any variable interpolation. This means that __<arg name="foo">this is ${someVar}</arg>__ would result in a mapping from "foo" to "this is [contents of someVar]".
* *The propertySet* contains all the persistent variables associated with the workflow instance.

Java-based functions are available in the following *types*:

### class

For a class function, the ClassLoader must know the class name of your function. This can be accomplished with the argument class.name. An example is:

```xml
<function type="class">
	<arg name="class.name">com.acme.FooFunction</arg>
	<arg name="message">The message is ${message}</arg>
</function>
```

### jndi

JNDI functions are just like class functions except they must already exist in the JNDI tree. Instead of a class.name argument, the argument *jndi.location* is required. Example:

```xml
<function type="jndi">
	<arg name="jndi.location">java:/FooFunction</arg>
	<arg name="message">The message is ${message}</arg>
</function>
```

### remote-ejb

Remote EJBs can be used as a function in OSWorkflow provided a few things happen first. The remote interface of the EJB must extend *com.opensymphony.workflow.FunctionProviderRemote*. Also, the required argument ejb.location must be given. Example:

```xml
<function type="remote-ejb">
	<arg name="ejb.location">java:/comp/env/FooEJB</arg>
	<arg name="message">The message is ${message}</arg>
</function>

### local-ejb

Local EJBs are exactly like remote EJBs, except that the local interface of the EJB must extend *com.opensymphony.workflow.FunctionProvider*, just like the other Java-based functions. Example:

```xml
<function type="local-ejb">
	<arg name="ejb.location">java:/comp/env/FooEJB</arg>
	<arg name="message">The message is ${message}</arg>
</function>
```

* Back to [Functions](functions.md)
* Forward to [BeanShell Functions](beanshell_functions.md)