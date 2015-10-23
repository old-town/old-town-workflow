# OSWorkflow - SOAP Support

* Back to [Conditions](conditions.md)
* Forward to [GUI Designer](gui_designer.md)

OSWorkflow comes with support for remote invocation using SOAP. This can be accomplished using either the Glue SOAP implementation from [WebMethods](http://www.webmethods.com/meta/default/folder/0000008629), or the open source [XFire](http://xfire.codehaus.org/) soap library.

## Using XFire

The example application that is bundled with OSWorkflow exposes the workflow object via SOAP by default, so you are encouraged to use that as a starting point. Enabling SOAP support is a fairly trivial matter. The first step is to ensure that you have all the required xfire jar files in your *WEB-INF/lib* directory. The files are all included in the *lib/optional/xfire* directory.

The next step is to add the SOAP servlet to your web application. Add the following to your web.xml file:

```xml
  <servlet>
    <servlet-name>SOAPWorkflow</servlet-name>
    <servlet-class>com.opensymphony.workflow.soap.SOAPWorkflowServlet</servlet-class>
  </servlet>
  
  <servlet-mapping>
    <servlet-name>SOAPWorkflow</servlet-name>
    <url-pattern>/soap/*</url-pattern>
  </servlet-mapping>
```

Once your application is deployed, you can access the WSDL at *http://<server>/soap/Workflow?wsdl*

For invoking the service, any SOAP client should work. XFire itself has [client support](http://xfire.codehaus.org/Client+API) that would enable you to use the same classes as the server. Other client libraries such as Axis, GLUE, or .net should also work out of the box.

## Using GLUE

GLUE does *not* come with OSWorkflow and must be downloaded separately from [WebMethods](http://www.webmethods.com/solutions/wM_Glue_OEM_ISV/). GLUE is generally free for most usage. You can find the license agreement when you download GLUE. *SOAP and Job Scheduling support will not be available if you do not download GLUE 2.1 or later and include GLUE-STD.jar in your classpath.*

As with XFire, the first step is adding the GLUE servlet to your web application, as detailed in the GLUE documentation. SOAP support *must* be enabled for scheduled jobs to occur, using the Quartz job scheduler. Here is some example code that uses Glue to talk to OSWorkflow:

```java
import electric.util.Context;
import electric.registry.Registry;
import electric.registry.RegistryException;

...

Context context = new Context();
context.setProperty( "authUser", username );
context.setProperty( "authPassword", password );
Workflow wf = (Workflow) Registry.bind(
  "http:<span class="code-comment">//localhost/osworkflow/glue/oswf.wsdl", Workflow.class, context);
```

From this point onward, you can use the Workflow interface just as you normally would.
