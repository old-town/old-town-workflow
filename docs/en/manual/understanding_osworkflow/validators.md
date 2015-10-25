# OSWorkflow - Validators

* Back to [Utility Functions](utility_functions.md)
* Forward to [Registers](registers.md)

Just like [Functions](functions.md), OSWorkflow allows for validators in three different forms: *Java-based*, *BeanShell*, and *BSF*. Java-based validators must implement the *com.opensymphony.workflow.Validator* interface (or in the case of *remote-ejb*'s, the *com.opensymphony.workflow.ValidatorRemote* interface). With Java-based validators, throwing an InvalidInputException is all that is needed to mark an input as invalid and stop the workflow action from occuring.

But in BeanShell and BSF, things are a little different, since exceptions thrown in the scripts can't propogate out to the Java runtime environment. To get around this, any value returned in a BeanShell or BSF script will be used as the error message(s). The logic is as follows:

* If the value returned is an InvalidInputException object, that object is immediately thrown to the client
* If the value returned is a Map, that Map is used for the error/errorMessage pairs in the InvalidInputException
* If the value returned is a String\[], the even numbers are used as keys, and the odd numbers are used as values to construct a Map that can then be used in the above case.
* Otherwise, the value is converted to a String and added as a generic error message.
