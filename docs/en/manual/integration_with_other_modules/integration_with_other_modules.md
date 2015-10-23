# OSWorkflow - Integration with Other Modules

* Back to [Loading Workflow Definitions](loading_workflow_definitions.md)
* Forward to [Spring framework](spring_framework.md)

## Integration with OSCore

OSWorkflow requires [PropertySet](http://www.opensymphony.com/propertyset), and [OSCore](http://www.opensymphony.com/oscore). Furthermore, OSWorkflow makes heavy usage of the many useful features in OSCore, and therefore OSCore version 2.2.0 or above is required to use OSWorkflow. OSUser is not strictly required, however, all of the built-in user/group functions and conditions use it, so you should include it unless you plan on writing your own versions of those conditions.

## Integration with PropertySet

One of the key features in OSWorkflow is the ability to save variables dynamically. This allows for a function (see [Functions](functions.md)) to take place on day 1 in the workflow lifecycle and store a piece of data in OSWorkflow. Then, many days later, when an action is executed in the workflow, that same data can be pulled up and re-used in another function. This is a very powerful feature that when used properly can allow for highly customized, long-lived workflow behavior that persists even after server restarts.

This is all possible by using the [PropertySet](http://www.opensymphony.com/propertyset) module. What kinds of variable types you can dynamically store in the propertyset (usually exposed as the variable *ps*) is totally up to the PropertySet implementation that is chosen by the *WorkflowStore* you have configured in __osworkflow.xml__. For example, if you choose the __JDBCWorkflowStore__, you must make sure that the jdbc propertyset is properly configured in __propertyset.xml__. Information on setting up the propertyset backend store (for example, sql scripts for the JDBCPropertySet) can be found in the propertyset download.
