# OSWorkflow - Executing actions
        
* Back to [Creating a new workflow](creating_a_new_workflow.md)
* Forward to [Queries](queries.md)

In OSWorkflow, executing an action is very simple:

```java
Workflow wf = new BasicWorkflow(username);
HashMap inputs = new HashMap();
inputs.put("docTitle", request.getParameter("title"));
long id = Long.parseLong(request.getParameter("workflowId"));
wf.doAction(id, 1, inputs);
```
