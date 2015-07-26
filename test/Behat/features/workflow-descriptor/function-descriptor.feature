Feature:Function Descriptor

@workflowDescriptor
Scenario: Create FunctionDescriptor the type of "class"
  Given Create descriptor "FunctionDescriptor" based on xml:
    """
      <function type="class">
        <arg name="class.name">TestClassName</arg>
        <arg name="testArg">testValue</arg>
      </function>
    """
  Then Call a method descriptor "getType", I get the value of "class"
    And Call a method descriptor "getArg", I get the value of "TestClassName". The arguments of the method:
      |name|
      |class.name|
    And Call a method descriptor "getArg", I get the value of "testValue". The arguments of the method:
      |name|
      |testArg|

@workflowDescriptor
Scenario: Create a descriptor from xml.
          Validate save in xml descriptor
  Given Create descriptor "FunctionDescriptor" based on xml:
  """
      <function type="class" id="1" name="testFunction">
        <arg name="class.name">TestClassName</arg>
        <arg name="testArg">testValue</arg>
      </function>
  """
  Then I save to descriptor xml. Compare with xml:
    """
      <function type="class" id="1" name="testFunction">
        <arg name="class.name">TestClassName</arg>
        <arg name="testArg">testValue</arg>
      </function>
  """

@workflowDescriptor
Scenario: Create FunctionDescriptor the type of "phpshell"
  Given Create descriptor "FunctionDescriptor" based on xml:
  """
      <function type="phpshell" id="1" name="testFunction">
        <arg name="script">echo 'test';</arg>
      </function>
  """
  Then I save to descriptor xml. Compare with xml:
    """
      <function type="phpshell" id="1" name="testFunction">
        <arg name="script"><![CDATA[echo 'test';]]></arg>
      </function>
  """

@workflowDescriptor
Scenario: Create FunctionDescriptor.
  Attempt to write without reference DOMDocument
  Given Create descriptor "FunctionDescriptor"
  Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"
