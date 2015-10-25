# OSWorkflow - 3.4.3 BSF Functions

* Back to [BeanShell Functions](beanshell_functions.md)
* Forward to [Utility Functions](utility_functions.md)

In addition to [Java-based Functions](java-based_functions.md) and [BeanShell Functions](beanshell_functions.md), OSWorkflow supports a third type of function: [Bean Scripting Framework](http://oss.software.ibm.com/developerworks/projects/bsf) functions. BSF is a project by IBM's AlphaWorks group that allows for commonly used languages such as VBScript, Perlscript, Python, and JavaScript to operate in a common environment. What this means in OSWorkflow is that you can code your functions in any language supported by BSF in the following manner:

```xml
<function type="bsf">
	<arg name="source">foo.pl</arg>
	<arg name="row">0</arg>
	<arg name="col">0</arg>
	<arg name="script">
		print $bsf->lookupBean("propertySet").getString("foo");
	</arg>
</function>
```

The above code gets the *propertySet* then prints out the value with the key "foo". The same variables that are in default scope in BeanShell functions are available to lookup in your BSF script. Please read the BSF guide for info on how to lookup these beans in your language of choice.
