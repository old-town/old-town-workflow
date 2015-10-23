# OSWorkflow - Palettes

* Back to [Workspaces](workspaces.md)
* Forward to [Using the API](using_the_api.md)

The designer is reasonably flexible in terms of allowing the deployer to specify the set of functions and conditions that are available to workflow editors.

A palette in designer terms is an xml file that consists of a number of conditions and functions that the user can choose from when creating a workflow. The palette defines the arguments that can be specified to a particular function or condition, as well as whether that argument is modifiable.

Note also that palettes are fully internationalized. All the strings are resource bundle keys. The actual text is specified in *palette.properties*.

There are a number of magic keys that are checked. For every function or condition name (for example, *check.status*), a .long key is used for its description (in our example, the key for description would be *check.status.long*).

For arguments, the naming convention for keys is <element name>.<arg name>. So for the check.status condition's status argument, the key in the properties file is *check.status.status*.

In all cases, if a particular key is not found, an appropriate fallback value is used (for example, the condition or arg name as listed in the xml file).

Currently the designer only supports one global palette. A default one with support for most of the built-in functions and conditions is shipped in the designer's META-INF directory. Deployers are encouraged to develop their own palettes for their functions and conditions.
