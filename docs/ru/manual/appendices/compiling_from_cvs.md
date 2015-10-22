# OSWorkflow - Compiling from CVS

To get the latest version of OSWorkflow, we recommend that you download the source via CVS and compile using the [Jakarta Ant](http://jakarta.apache.org/ant/index.html) build script provided there. Compiling OSWorkflow from source is trivial with the following two ant targets:</p>

* *jar* __(default)__ - compiles the library *osworkflow.jar*
* *example-war* - compiles and assembles *osworkflow-2.8.0-example.war*, which is configured to use memory persistence and should deploy without any configuration into most web containers.
* *example-ear* - compiled and assembles *osworkflow-2.8.0-example.ear*, which is a J2EE application that is configured to use osworkflow with the EJB persistence store. Some configuration of data sources is required, as well as a full J2EE application server.
* *client-jar* - compiled and assembles *designer.jar*, which is a Swing GUI workflow designer application that can be used to visually inspect and edit workflows.