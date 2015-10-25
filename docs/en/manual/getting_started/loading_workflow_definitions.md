# OSWorkflow - Loading Workflow Definitions

* Back to [Persistence Options](persistence_options.md)
* Forward to [Integration with Other Modules](integration_with_other_modules.md)

OSWorkflow tries to be as flexible as possible with regards to configuration. Only one file is required to be in the classpath: [osworkflow.xml](osworkflow.xml). This file dictates the persistence method (JDBC, EJB, Ofbiz) as well the workflow factory class that is to be used for loading workflow definitions.

The default factory is *com.opensymphony.workflow.loader.XMLWorkflowFactory*. This loads up a file in the classpath that in turn contains links to any number of workflow definition files, all in XML ([see Appendix A](http://www.opensymphony.com/osworkflow/workflow_2_6.dtd)).

During test phase it's also useful to have your workflow definition files reloaded as they are changed. For this you can specify an optional property called *reload* (true|false) to your factory definition (default is false). If you would rather specify your workflow definitions in a different way, you are free to extend [com.opensymphony.workflow.loader.AbstractWorkflowFactory](http://www.opensymphony.com/osworkflow/api/com/opensymphony/workflow/loader/AbstractWorkflowFactory.html) in any way that you like.

*com.opensymphony.workflow.loader.JDBCWorkflowFactory* for example is an alternative factory, that allows you to store your workflow definitions in a JDBC database instead of putting them into the xml files. ([JDBCWorkflowFactory](JDBCWorkflowFactory.html))

OSWorkflow also includes a workflow factory that stores a normalized form of the workflow descriptor in a relational DB. This factory uses Spring and Hibernate. See [SpringHibernateWorkflowFactory](SpringHibernateWorkflowFactory.html).

The most common configuration would be:

### osworkflow.xml:

```java
<osworkflow>
  <persistence class="com.opensymphony.workflow.spi.jdbc.JDBCWorkflowStore">
    <arg name="foo" value="bar"/>
    ...
  </persistence>
  <factory class="com.opensymphony.workflow.loader.XMLWorkflowFactory">
    <property key="resource" value="workflows.xml" />
  </factory>
</osworkflow>
```

### workflows.xml:

```java
<workflows>
  <workflow name="example" type="resource" location="example.xml"/>
</workflows>
```


* Back to [Persistence Options](persistence_options.md)
* Forward to [Integration with Other Modules](integration_with_other_modules.md)