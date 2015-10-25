# OSWorkflow - Interface choices

* Back to [Palettes](palettes.md)
* Forward to [Creating a new workflow](creating_a_new_workflow.md)

OSWorkflow provides several implementations of the *com.opensymphony.workflow.Workflow* interface that can be used in your application.

## BasicWorkflow

The BasicWorkflow has no transactional support, though depending upon your persistence implementation, transactional support can be wrapped around this. It is created by doing  ```java Workflow wf = new BasicWorkflow(username) ``` where username is the user who is associated with the current request.

## EJBWorkflow

The EJB workflow uses the EJB container to manage transactions. This is configured in *ejb-jar.xml*. It is created by doing ```java Workflow wf = new EJBWorkflow()```. There is no need to give the username (as in BasicWorkflow and OfbizWorkflow) since that is automatically pulled in from the EJB container once the user has been authorized.

## OfbizWorkflow

The OfbizWorkflow is exactly like the BasicWorkflow in every way, except that methods that require transactional support are wrapped with ofbiz TransactionUtil calls.
