# OSWorkflow - Implicit vs Explicit Configuration
        
* Back to [Queries](queries.md)

Previous to OSWorkflow 2.7, state was maintained using static fields in a number of places. This approach while convenient, has quite a few drawbacks and restrictions. One of the main ones was the fact that it was not possible to have multiple instances of OSWorkflow with different configurations running. Put simply, you couldn't have a number of workflows using a memory store running alongside other workflow instances that used the EJB store, for example.

OSWorkflow 2.7 fixed this limitation by the introduction of a Configuration interface. The default implementation of this interface is DefaultConfiguration, which mimics the backward compatible behaviour of loading resources and so on. Also for the sake of backward compatibility, a static instance is used <b>if no explicit call is made using a Configuration</b>. Practically speaking, the decision to use the static instance or a specified configuration is determined by the <em>setConfiguration</em> method of AbstractWorkflow. If the method is called, then the per-instance model is used. If it is not called, then the legacy singleton static model is used.

One aspect of the new approach is that the AbstractWorkflow object is no longer stateless, and if you do not use the static approach (which you are dicouraged from using now!), you need to hold onto the instance of AbstractWorkflow that you instatiate and reuse it, instead of creating a new one for every call.

While that might all sound rather complicated, in practice it is quite simple, as the following examples illustrate:

## Legacy approach:

```java
Workflow workflow = new BasicWorkflow("blah");
long workflowId = workflow.initialize("someflow", 1, new HashMap());
workflow.doAction(workflowId, 2, new HashMap());
...
//in some other class, called later on
Workflow workflow = new BasicWorkflow("blah");
workflow.doAction(workflowId, 3, new HashMap());
```

## Recommend approach:

```java
Workflow workflow = new BasicWorkflow("blah");
Configuration config = new DefaultConfiguration();
workflow.setConfiguration(config);
long workflowId = workflow.initialize("someflow", 1, new HashMap());
workflow.doAction(workflowId, 2, new HashMap());
//keep track of Workflow object somewhere!
...
//in some other class, called later on
//look up Workflow instance that was held onto earlier
Workflow workflow = ...; //note, do NOT create a new one!
workflow.doAction(workflowId, 3, new HashMap());
```
