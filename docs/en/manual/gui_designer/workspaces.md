#OSWorkflow - Workspaces

* Back to [Quick Start Guide](quick_start_guide.md)
* Forward to [Palettes](palettes.md)

A workspace in the designer is any collection of workflows. A workspace is a convenient grouping that allows you to work on multiple workflows at a time. It is somewhat analogous with a 'project'. All of the files associated with a workspace are created in the same directory as the master configuration file, which is why it is strongly recommended that when creating a workspace, you do so in an empty directory.

In terms of implementation, a workspace is a type of WorkflowFactory, in that it knows how to load and save workflows, as well as providing extra functionality to handle the storage of workflow layout data.
