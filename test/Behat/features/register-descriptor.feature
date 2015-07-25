Feature:Register Descriptor

@workflowDescriptor
Scenario: Create RegisterDescriptor the type of "class"
  Given Create descriptor "RegisterDescriptor" based on xml:
    """
        <register type="class" variable-name="log" id="1">
            <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
            <arg name="addInstanceId">true</arg>
        </register>
    """
  Then Call a method descriptor "getVariableName", I get the value of "log"
    And Call a method descriptor "getType", I get the value of "class"
    And Call a method descriptor "getId", I get the value of "1"
    And Call a method descriptor "getArg", I get the value of "\OldTown\Workflow\Util\LogRegister". The arguments of the method:
      |name|
      |class.name|
    And Call a method descriptor "getArg", I get the value of "true". The arguments of the method:
      |name|
      |addInstanceId|

@workflowDescriptor
Scenario: Create a descriptor from xml.
          Validate save in xml descriptor
  Given Create descriptor "RegisterDescriptor" based on xml:
  """
      <register type="class" variable-name="log" id="1">
          <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
          <arg name="addInstanceId">true</arg>
      </register>
  """
  Then I save to descriptor xml. Compare with xml:
    """
      <register type="class" variable-name="log" id="1">
          <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
          <arg name="addInstanceId">true</arg>
      </register>
  """

@workflowDescriptor
Scenario: Create RegisterDescriptor the type of "phpshell"
  Given Create descriptor "RegisterDescriptor" based on xml:
  """
      <register type="phpshell" variable-name="log">
        <arg name="script">echo 'test';</arg>
      </register>
  """
  Then I save to descriptor xml. Compare with xml:
    """
      <register type="phpshell" variable-name="log">
        <arg name="script"><![CDATA[echo 'test';]]></arg>
      </register>
  """

@workflowDescriptor
Scenario: Create RegisterDescriptor.
    Test variable-name attribute
  Given Create descriptor "RegisterDescriptor"
  When Call a method descriptor "setVariableName". The arguments of the method:
    |variableName|
    |test-variable-name|
  Then Call a method descriptor "getVariableName", I get the value of "test-variable-name"

@workflowDescriptor
Scenario: Create RegisterDescriptor.
  Attempt to write without reference DOMDocument
  Given Create descriptor "RegisterDescriptor"
  Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"

@workflowDescriptor
Scenario: Create RegisterDescriptor.
  Unknown attribute - variable-name
  Given Create descriptor "RegisterDescriptor"
  When Call a method descriptor "setType". The arguments of the method:
    |type|
    |class|
  Then I save to descriptor xml. I expect to get an exception "\OldTown\Workflow\Exception\InvalidDescriptorException"

@workflowDescriptor
Scenario: Create RegisterDescriptor.
  Unknown attribute - type
  Given Create descriptor "RegisterDescriptor"
  When Call a method descriptor "setVariableName". The arguments of the method:
    |variableName|
    |test-variable-name|
  Then I save to descriptor xml. I expect to get an exception "\OldTown\Workflow\Exception\InvalidDescriptorException"