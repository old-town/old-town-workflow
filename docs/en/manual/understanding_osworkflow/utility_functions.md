#OSWorkflow - Utility Functions

* Back to [3.4.3 BSF Functions](bsf_functions.md)
* Forward to [3.5 Validators](validators.md)

OSWorkflow comes with several very useful utility functions, all implementing the interface __com.opensymphony.workflow.FunctionProvider__. For more detailed information, please see the javadocs for these utility functions. Below is only a brief description of each utility function. All classes are found in the __com.opensymphony.workflow.util__ package.

## Caller

Sets the transient variable caller with the username of the person doing the current action.

## WebWorkExecutor

Executes a WebWork function and restores the old ActionContext when finished.

## EJBInvoker

Invokes an EJB session bean method. Please see the javadocs for more information about expected arguments and EJB restrictions.

## JMSMessage

Sends a TextMessage to a JMS topic or queue.

## MostRecentOwner

Sets the transient variable *mostRecentOwner* with the username of the owner of the most recent step specified. Optional features allow for the variable be set to nothing if no owner is found, or to return with an internal error.

## ScheduleJob

Schedules a [Trigger functions](trigger_functions.md) to be executed at some time later. Supports both *cron expressions* and *simple repeat/delay counts*.

## UnschduleJob

Deletes a scheduled job and all triggers associated with that job. This is useful in the case where the workflow state has changed such that you no longer wish for scheduled jobs to occur.

## SendEmail

Sends out an email to one or more users.

* Back to [3.4.3 BSF Functions](bsf_functions.md)
* Forward to [3.5 Validators](validators.md)
