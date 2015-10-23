#OSWorkflow - Persistence Options

* Back to [Running the Example App](running_the_example_app.md)
* Forward to [Loading Workflow Definitions](loading_workflow_definitions.md)

OSWorkflow provides a pluggable persistence mechanism that can be used to allow for many choices of ways for workflow content to be stored. Included with OSWorkflow are the following WorkflowStore implementations: *MemoryStore (default), SerializableStore, JDBCStore, OfbizStore, and EJBStore*. If one of these does not fit your requirements, you can implement your own workflow store by implementing the interface [com.opensymphony.workflow.spi.WorkflowStore](http://www.opensymphony.com/osworkflow/api/com/opensymphony/workflow/spi/WorkflowStore.html). See the javadocs for more information.

Also, please note that each workflow store implementation may have required or optional properties that must or can be set. It is recommend that you read the javadocs for the workflow store that you plan to use so that you can configure it correctly. A sample JDBC configuration for the JDBCStore is given below:

(from osworkflow.xml)


```xml
<persistence class="com.opensymphony.workflow.spi.jdbc.JDBCWorkflowStore">
	<!-- For jdbc persistence, all are required. -->
	<property key="datasource" value="jdbc/DefaultDS"/>
	<property key="entry.sequence" 
                      value="SELECT nextVal('seq_os_wfentry')"/>
	<property key="entry.table" value="OS_WFENTRY"/>
	<property key="entry.id" value="ID"/>
	<property key="entry.name" value="NAME"/>
	<property key="entry.state" value="STATE"/>
	<property key="step.sequence" 
                       value="SELECT nextVal('seq_os_currentsteps')"/>
	<property key="history.table" value="OS_HISTORYSTEP"/>
	<property key="current.table" value="OS_CURRENTSTEP"/>
	<property key="historyPrev.table" value="OS_HISTORYSTEP_PREV"/>
	<property key="currentPrev.table" value="OS_CURRENTSTEP_PREV"/>
	<property key="step.id" value="ID"/>
	<property key="step.entryId" value="ENTRY_ID"/>
	<property key="step.stepId" value="STEP_ID"/>
	<property key="step.actionId" value="ACTION_ID"/>
	<property key="step.owner" value="OWNER"/>
	<property key="step.caller" value="CALLER"/>
	<property key="step.startDate" value="START_DATE"/>
	<property key="step.finishDate" value="FINISH_DATE"/>
	<property key="step.dueDate" value="DUE_DATE"/>
	<property key="step.status" value="STATUS"/>
	<property key="step.previousId" value="PREVIOUS_ID"/>
</persistence>
```

If you are using tomcat as the servlet container, you should config *$TOMCAT_HOME/conf/server.xml* for data source, by default, the data source name is *jdbc/DefaultDS*.

You should also config the propertyset to use jdbc by adding  __WEB-INF/classes/propertyset.xml__, please note that the datasource setting should match the name defined in tomcat.</p>

```xml
<propertysets>
    <propertyset name="jdbc" 
      class="com.opensymphony.module.propertyset.database.JDBCPropertySet">
        <arg name="datasource" value="jdbc/DefaultDS"/>
        <arg name="table.name" value="OS_PROPERTYENTRY"/>
        <arg name="col.globalKey" value="GLOBAL_KEY"/>
        <arg name="col.itemKey" value="ITEM_KEY"/>
        <arg name="col.itemType" value="ITEM_TYPE"/>
        <arg name="col.string" value="STRING_VALUE"/>
        <arg name="col.date" value="DATE_VALUE"/>
        <arg name="col.data" value="DATA_VALUE"/>
        <arg name="col.<span class="code-object">float" value="FLOAT_VALUE"/>
        <arg name="col.number" value="NUMBER_VALUE"/>
    </propertyset>
</propertysets>
```

A number of sample sql scripts to create the required tables are included in OldTown Workflow distribution in in the src/etc/deployment/jdbc directory.

If HypersonicSQL is used as the datasource, you can follow these steps:

1) Assume your hsql db is named *oswf* and created in directory */db*
2) Use the hsql.sql script to create the tables, you can use *java -cp hsqldb.jar org.hsqldb.util.DatabaseManager* to startup the tool to execute the sript.
3) Add the context config to *$TOMCAT_HOME/conf/server.xml*

```xml
<Context path="/osworkflow" 
         docBase="/jakarta-tomcat-4.1.27/webapps/osworkflow-2.8.0-example">
          <Resource name="jdbc/oswf" type="javax.sql.DataSource"/>
          <ResourceParams name="jdbc/DefaultDS">
            <parameter><name>username</name><value>sa</value></parameter>
            <parameter><name>password</name><value></value></parameter>
            <parameter><name>driverClassName</name>
              <value>org.hsqldb.jdbcDriver</value></parameter>
            <parameter><name>url</name>
              <value>jdbc:hsqldb:/db/oswf</value></parameter>
          </ResourceParams>
</Context>
```

4) Add *WEB-INF/classes/propertyset.xml* as described above

5) Change the persistent setting of __WEB-INF/classes/osworkflow.xml__. This example below should be used for any database that does not support sequences (eg, HSQL)


```xml
<persistence class="com.opensymphony.workflow.spi.jdbc.JDBCWorkflowStore">
	<!- For jdbc persistence, all are required. -->
	<property key="datasource" value="jdbc/DefaultDS"/>
	<property key="entry.sequence" 
                       value="select count(*) + 1 from os_wfentry"/>
	<property key="entry.table" value="OS_WFENTRY"/>
	<property key="entry.id" value="ID"/>
	<property key="entry.name" value="NAME"/>
	<property key="entry.state" value="STATE"/>
	<property key="step.sequence" value="select sum(c1) from 
(select 1 tb, count(*) c1 from os_currentstep 
union select 2 tb, count(*) c1 from os_historystep)"/>
	<property key="history.table" value="OS_HISTORYSTEP"/>
	<property key="current.table" value="OS_CURRENTSTEP"/>
	<property key="historyPrev.table" value="OS_HISTORYSTEP_PREV"/>
	<property key="currentPrev.table" value="OS_CURRENTSTEP_PREV"/>
	<property key="step.id" value="ID"/>
	<property key="step.entryId" value="ENTRY_ID"/>
	<property key="step.stepId" value="STEP_ID"/>
	<property key="step.actionId" value="ACTION_ID"/>
	<property key="step.owner" value="OWNER"/>
	<property key="step.caller" value="CALLER"/>
	<property key="step.startDate" value="START_DATE"/>
	<property key="step.finishDate" value="FINISH_DATE"/>
	<property key="step.dueDate" value="DUE_DATE"/>
	<property key="step.status" value="STATUS"/>
	<property key="step.previousId" value="PREVIOUS_ID"/>
</persistence>
```

Note that the exact query for step.sequence and entry.sequence might vary in order to use an appropriate sequencing native DB mechanism.

For example, in MSSQL the correct step.sequence value would be (assuming you're not using a database sequence):

```xml
<property key="step.sequence" value="select sum(c1) + 1 from (select 1 as
tb, count(*) as c1 from os_currentstep union select 2 as tb, count(*) as c1
from os_historystep) as TabelaFinal" />
```

For MYSQL, OSWorkflow provides a custom store that can be used. This schema uses a separate table to store ID values (the schema is listed in the mysql.sql file).

In addition to this, two other changes from a standard deployment must be specified. The first are the calls to access the ID sequences. There are specified in the store properties in osworkflow.xml. The elements to be added are:

```xml
  <property key="step.sequence.increment" 
    value="INSERT INTO OS_STEPIDS (ID) values (null)"/>
  <property key="step.sequence.retrieve" 
    value="SELECT max(ID) FROM OS_STEPIDS"/>
  <property key="entry.sequence.increment" 
    value="INSERT INTO OS_ENTRYIDS (ID) values (null)"/>
  <property key="entry.sequence.retrieve" 
    value="SELECT max(ID) FROM OS_ENTRYIDS"/>
```

In the same file, the store factory specified should be *com.opensymphony.workflow.spi.jdbc.MySQLWorkflowStore*

* Back to [Running the Example App](running_the_example_app.md)
* Forward to [Loading Workflow Definitions](loading_workflow_definitions.md)
