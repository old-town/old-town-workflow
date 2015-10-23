# OSWorkflow - Functions

* Back to [Common and Global Actions](common_and_global_actions.md)
* Forward to [Java-based Functions](java-based_functions.md)

Functions in OSWorkflow are where you can perform the "meat" of your workflow-based application. They can be executed before and after (pre- and post-) transitions from one state to another in the finite state machine. OSWorkflow supports the following forms of functions:

### [Java-based Functions](java-based_functions.md)

* Java classes loaded by a ClassLoader
* Java classes retrieved via JNDI
* Remote EJBs
* Local EJBs

### [BeanShell Functions](beanshell_functions.md)

### [BSF Functions](bsf_functions.md) (perlscript, vbscript, javascript)

### [Utility Functions](utility_functions.md)

OSWorkflow also includes a small set of [Utility Functions](utility_functions.md) that will start you off in the right direction. Some of these utility functions are extremely valuable in creating dynamic workflow definitions.
