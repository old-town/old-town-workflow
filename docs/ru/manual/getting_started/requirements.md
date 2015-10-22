# OSWorkflow - Requirements

* Back to [1.1 Introduction](introduction.md)
* Forward to [1.3 Running the Example App](running_the_example_app.md)

Almost all of the required libraries for OSWorkflow are included in the distribution:

* [OSCore 2.0.1+](http://www.opensymphony.com/oscore)
* [PropertySet 1.2+](http://www.opensymphony.com/propertyset)
* [Jakarta commons-logging](http://jakarta.apache.org/commons/logging.html)
* [BeanShell](http://www.beanshell.org/) (optional)
* [BSF](http://oss.software.ibm.com/developerworks/projects/bsf) (optional)
* EJB interfaces (not neccesarily an EJB container)
* XML parser (Not required for JDK 1.4)

The core API of OSWorkflow will work with JDK 1.3+. However, the GUI designer application required a 1.4 JVM.

*A note about SOAP and job scheduling:* GLUE is one of the SOAP implementations that OSWorkflow uses. You can also use XFire as of OSWorkflow 2.8. GLUE is freely available from [WebMethods](http://www.webmethods.com/solutions/wM_Glue_OEM_ISV/). If you are going to require SOAP support or *remote* Job Scheduling support, you should download the *GLUE Professional* libraries. [XFire](http://xfire.codehaus.org) is an open source Codehaus project.

In addition to GLUE, you'll also need [Quartz](http://www.part.net/quartz.html) for job scheduling. If you don't wish to use GLUE and/or Quartz, you can provide alternate implementations very easily by using OldTown Workflow API. If you are going to run Quartz from within your application server or any place that has OSWorkflow properly configured, you do not need GLUE and must configure the JobScheduler to have the arg "local" set to __true__.

In addition to the above libraries, you will have different required libraries or install bases based on the persistence mechanism (WorkflowStore) you choose to use in your application. You can read more about these requirements in the [Persistence Options](persistence_options.md) section. You may also need other libraries based on any utility functions you decide to use. For example, if you use the *OSUserGroupCondition* you will need [OSUser](http://www.opensymphony.com/osuser) installed as well.
