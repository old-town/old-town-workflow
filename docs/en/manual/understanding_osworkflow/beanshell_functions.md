# OSWorkflow - BeanShell Functions

* Back to [Java-based Functions](java-based_functions.md)</li>
* Forward to [BSF Functions](bsf_functions.md)

OSWorkflow supports BeanShell as a scripting language. You can find out more about BeanShell at the [http://www.beanshell.org/](BeanShell website). Reading the documentation for BeanShell should be more than enough to get started with scripting for your own workflow definition file. There are a few behaviors that you should be aware of before starting, however:

The *type* that must be chosen for BeanShell functions is *beanshell*. There is one required argument: *script*. The value for this argument is the actual script that is to be executed. Example:

```xml
<function type="beanshell">
	<arg name="script">
		System.out.println("Hello, World!");
	</arg>
</function>
```

There are three variables in the expression scope at all times: __entry__ __context__, and __store__. The variable "entry" is an object that implements __com.opensymphony.workflow.spi.WorkflowEntry__ and represents the workflow instance. The variable "context" is an object of type __com.opensymphony.workflow.WorkflowContext__ which allows for BeanShell functions to roll back transactions or determine the caller name. The "store" variable is of type __com.opensymphony.workflow.WorkflowStore__ and allows the function to access the underlying workflow persistence store.

Just like [Java-based Functions](java-based_functions.md), there are three variables that can be used and are automatically set in the BeanShell scope: *transientVars*, *args*, and *propertySet*. The same rules that apply to Java functions also apply here. Example:</p>

```xml
<function type="beanshell">
	<arg name="script">	
		propertySet.setString("world", "Earth");
	</arg>
</function>
<function type="beanshell">
	<arg name="script">	
		System.out.println("Hello, "+propertySet.getString("world"));
	</arg>
</function>
```

The output of these two scripts would be "Hello, Earth". This is because any variable stored in the *propertySet* is persisted for use in functions later in the workflow.

* Back to [Java-based Functions](java-based_functions.md)
* Forward to [BSF Functions](bsf_functions.md)