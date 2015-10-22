# OSWorkflow - Queries

* Back to [Executing actions](executing_actions.md)
* Forward to [Implicit vs Explicit Configuration](implicit_vs_explicit_configuration.md)

OSWorkflow 2.6 introduces a new [ExpressionQuery](http://www.opensymphony.com/osworkflow/api/com/opensymphony/workflow/query/WorkflowExpressionQuery.html) API.

Note that not all workflow stores support queries. Currently the Hibernate, JDBC, and Memory workflow stores do support queries. The hibernate store however does not support mixed-type queries (for example, a query that uses both the history and current step contexts). To execute a query, a WorkflowExpressionQuery object is constructed, and the [query](http://www.opensymphony.com/osworkflow/api/com/opensymphony/workflow/Workflow.html#query\(com.opensymphony.workflow.query.WorkflowExpressionQuery)) method is invoked on the Workflow object.

Below are some query example:

```java
  //Get all workflow entry ID's for which the owner is 'testuser'
new WorkflowExpressionQuery(
  new FieldExpression(FieldExpression.OWNER, //Check the OWNER field
  FieldExpression.CURRENT_STEPS,  //Look in the current steps context
  FieldExpression.EQUALS, //check equality
  "testuser")); //the equality value is 'testuser'
```

```java
//Get all workflow entry ID's that have the name 'myworkflow'
new WorkflowExpressionQuery(
  new FieldExpression(FieldExpression.NAME, //Check the NAME field
  FieldExpression.ENTRY,  //Look in the entries context
  FieldExpression.EQUALS,  //Check equality
  'myworkflow')) //equality value is 'myworkflow'
```

Below is an example of a nested query:

```java
// Get all finished workflow entries where the current owner is 'testuser'
Expression queryLeft = new FieldExpression(
  FieldExpression.OWNER, 
  FieldExpression.CURRENT_STEPS, 
  FieldExpression.EQUALS, 'testuser');
Expression queryRight = new FieldExpression(
  FieldExpression.STATUS, 
  FieldExpression.CURRENT_STEPS, 
  FieldExpression.EQUALS, 
  "Finished", 
  true);
WorkflowExpressionQuery query = new WorkflowExpressionQuery(
  new NestedExpression(new Expression[] {queryLeft, queryRight},
  NestedExpression.AND));
```

Finally, here is an example of a mixed-context query. Note that this query is not supported by the Hibernate workflow store.

```java
//Get all workflow entries that were finished in the past
//or are currently marked finished
Expression queryLeft = new FieldExpression(
  FieldExpression.FINISH_DATE, 
  FieldExpression.HISTORY_STEPS, 
  FieldExpression.LT, new Date());
Expression queryRight = new FieldExpression(
  FieldExpression.STATUS, 
  FieldExpression.CURRENT_STEPS, 
  FieldExpression.EQUALS, "Finished");
WorkflowExpressionQuery query = new WorkflowExpressionQuery(
  new NestedExpression(new Expression[] {queryLeft, queryRight},
  NestedExpression.OR));
```
