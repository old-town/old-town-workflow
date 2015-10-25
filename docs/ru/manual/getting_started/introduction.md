#OSWorkflow - Introduction

* Forward to [Requirements](requirements.md)

OSWorkflow is fairly different from most other workflow systems available, both commercially and in the open source world. What makes OSWorkflow different is that it is extremely flexible. This can be hard to grasp at first, however. For example, OSWorkflow does not mandate a graphical tool for developing workflows, and the recommended approach is to write the xml workflow descriptors 'by hand'. It is up to the application developer to provide this sort of integration, as well as any integration with existing code and databases. These may seem like problems to someone who is looking for a quick "plug-and-play" workflow solution, but we've found that such a solution never provides enough flexibility to properly fulfill all requirements in a full-blown application.

**OSWorkflow gives you this flexibility.**

OSWorkflow can be considered a "low level" workflow implementation. Situations like "loops" and "conditions" that might be represented by a graphical icon in other workflow systems must be "coded" in OSWorkflow. That's not to say that actual code is needed to implement situations like this, but a scripting language must be employed to specify these conditions. It is not expected that a non-technical user modify workflow. We've found that although some systems provide GUIs that allow for simple editing of workflows, the applications surrounding the workflow usually end up damaged when changes like these are made. We believe it is best for these changes to be made by a developer who is aware of each change. Having said that, the latest version provides a GUI designer that can help with the editing of the workflow.

OSWorkflow is based heavily on the concept of the _finite state machine_. Each state in represented by the combination of a step ID and a status. A **transition** from one state to another cannot happen without an action occuring first. There are always at least one or more active states during the lifetime of a workflow. These simple concepts are what lie at the core of OldTown Workflow engine and allow a simple XML file to be translated in to business workflow processes.

* Forward to [Requirements](requirements.md)