Feature:Validator Descriptor

@workflowDescriptor
Scenario: Create ValidatorDescriptor the type of "class"
  Given Create descriptor "ValidatorDescriptor" based on xml:
    """
        <validator type="class" name="validator-name" id="1">
            <arg name="class.name">TestValidatorClass</arg>
            <arg name="addInstanceId">true</arg>
        </validator>
    """
  Then Call a method descriptor "getName", I get the value of "validator-name"
    And Call a method descriptor "getType", I get the value of "class"
    And Call a method descriptor "getId", I get the value of "1"
    And Call a method descriptor "getArg", I get the value of "TestValidatorClass". The arguments of the method:
      |name|
      |class.name|
    And Call a method descriptor "getArg", I get the value of "true". The arguments of the method:
      |name|
      |addInstanceId|

@workflowDescriptor
Scenario: Create a descriptor from xml.
          Validate save in xml descriptor
  Given Create descriptor "ValidatorDescriptor" based on xml:
  """
        <validator type="class" name="validator-name" id="1">
            <arg name="class.name">TestValidatorClass</arg>
            <arg name="addInstanceId">true</arg>
        </validator>
  """
  Then I save to descriptor xml. Compare with xml:
    """
        <validator type="class" name="validator-name" id="1">
            <arg name="class.name">TestValidatorClass</arg>
            <arg name="addInstanceId">true</arg>
        </validator>
  """

@workflowDescriptor
Scenario: Create ValidatorDescriptor the type of "phpshell"
  Given Create descriptor "ValidatorDescriptor" based on xml:
  """
      <validator type="phpshell" name="validator-name" id="1">
        <arg name="script">echo 'test';</arg>
      </validator>
  """
  Then I save to descriptor xml. Compare with xml:
    """
      <validator type="phpshell" name="validator-name" id="1">
        <arg name="script"><![CDATA[echo 'test';]]></arg>
      </validator>
  """


@workflowDescriptor
Scenario: Create ValidatorDescriptor.
  Attempt to write without reference DOMDocument
  Given Create descriptor "ValidatorDescriptor"
  Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"


@workflowDescriptor
Scenario: Create ValidatorDescriptor.
  Unknown attribute - type
  Given Create descriptor "ValidatorDescriptor"
  Then I save to descriptor xml. I expect to get an exception "\OldTown\Workflow\Exception\InvalidDescriptorException"