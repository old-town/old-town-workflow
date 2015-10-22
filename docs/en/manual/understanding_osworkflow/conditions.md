# OSWorkflow - Conditions

* Back to [Registers](registers.md)
* Forward to [SOAP Support](soap_support.md)

Conditions in OSWorkflow are similar to functions with one minor exception: *in BSF and Beanshell scripts, there is an extra object in scope of the name "jn"*. This variable is of the class [com.opensymphony.workflow.JoinNodes](http://www.opensymphony.com/osworkflow/api/com/opensymphony/workflow/JoinNodes.html) and is used for join-conditions. Other than that, conditions are just like functions except that they must return a value that equates to *true or false*. This includes a String "true" or a Boolean that evaluates to true, or any other object that has a toString() method that returns the String "true" in some manner (TRUE, True, true, etc).

Each *condition* must be defined as a child of the *conditions* element. This element has one attribute called *type*, which can either be *AND* or *OR*. When using the type AND all the condition elements must evaluate to true or the overall condition will return as false. When using the type OR only one condition element must evaluate to true for the overall condition to pass. If you require more complex conditional logic, then consider implementing it yourself using the *Condition* or *ConditionRemote* interfaces, BeanShell, or BSF. Note that the type can be omitted if the conditions element contains only one condition.

As of OSWorkflow 2.7, it is possible to nest conditions by simply specifying additional <conditions> children elements under a <conditions> element, which enables you to express more complex logical operations than a simple AND or OR on a set of conditions on the same level.

Below is a list of some of the standard conditions that OSWorkflow ships with:

* *OSUserGroupCondition* - Uses OSUser to determine if the caller is in the required argument "group"
* *StatusCondition* - Determines if the current step's status if the same as the required argument "status".
* *AllowOwnerOnlyCondition* - Only returns true if the caller is the owner of the specified current step or any current step if the step is not specified.
* *DenyOwnerCondition* - Does the opposite of AllowOwnerOnlyCondition.

You can read the JavaDocs of these classes for more information.