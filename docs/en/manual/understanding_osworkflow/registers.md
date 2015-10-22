#OSWorkflow - Registers

* Back to [3.5 Validators](validators.md)
* Forward to [3.7 Conditions](conditions.md)

A register in OSWorkflow is a runtime field that can be dynamically registered in the workflow definition file.

Registers are useful in a number of situations. For example, you might want to provide easy access to the entity that is progressing through the workflow (separate from the actual workflow itself) to the workflow descriptor. In this case, you would define a register that encapsulates this entity. If this entity happens to be a local session EJB, you could use the *com.opensymphony.workflow.util.ejb.local.LocalEJBRegister* register class to make this entity available. Later on in a particular post-function, you will have access to the entity, and can call any of its methods through a beanshell script, for example.

Registers, just like [Validators](validators.md) and [Functions](functions.md), can be implemented using three different forms: *Java-based*, *BeanShell*, and *BSF*.

### Java-based

Java-based registers must implement the *com.opensymphony.workflow.Register* interface (or in the case of *remote-ejb*'s, the *com.opensymphony.workflow.RegisterRemote* interface).

### BeanShell and BSF registers

The value or object returned by the script will be the object that is registered.

### Register interface note

While validators and functions both have the three parameters (*transientVars*, *args*, and *propertySet*) to work with, registers only have the args Map to use (along with the *context* and *entry* variables that are found in the *propertySet* normally). This is because registers are called regardless of user input, and also make up the variables map, so it would not make sense to give them scope to that kind of information.

### An example

The following example will help illustrate register functionality and usage. The register used here is a simple logging register, which is basically a register that exposes a 'log' variable that can then be accessed during the lifetime of the workflow. The logger does a couple of useful things like add the workflow instance id to the logged message.

We specify the register at the top of the workflow descriptor:


```xml
  <registers>
    <register type="class" variable-name="log">
      <arg name="class.name">com.opensymphony.workflow.util.LogRegister</arg>
      <arg name="addInstanceId">true</arg>
    </register>
  </registers>
```

As can be seen from the code, we create a LogRegister, with the name 'log', and specify a parameter of 'addInstanceId' with the value 'true'.

We can now use this variable anywhere in the workflow descriptor. For example:


```xml
<function type="beanshell" name="bsh.function">
  <arg name="script">transientVars.get("log").info("function called");</arg>
</function>
```

This will print out 'function called', with the workflow instance ID prepended to the output.

While this example is fairly trivial, it does illustrate the power of registers, and highlights how they can be used to allow access to specific entities or data during the lifetime of a workflow.
