Feature:Condition Descriptor

  @workflowDescriptor
  Scenario: Create ConditionDescriptor the type of "class"
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class" id="7" name="test-name">
          <arg name="class.name">TestConditionDescriptorClassName</arg>
          <arg name="testArg">testValue</arg>
      </condition>
    """
    Then  Call a method descriptor "isNegate", I get the value of "(boolean)false"
      And Call a method descriptor "getId", I get the value of "7"
      And Call a method descriptor "getName", I get the value of "test-name"
      And Call a method descriptor "getType", I get the value of "class"
      And Call a method descriptor "getArg", I get the value of "TestConditionDescriptorClassName". The arguments of the method:
        | name       |
        | class.name |
      And Call a method descriptor "getArg", I get the value of "testValue". The arguments of the method:
        | name    |
        | testArg |

  @workflowDescriptor
  Scenario: Create ConditionDescriptor the type of "class". Test default value attribute
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class" />
    """
    Then  Call a method descriptor "isNegate", I get the value of "(boolean)false"
      And Call a method descriptor "getId", I get the value of "(null)null"
      And Call a method descriptor "getName", I get the value of "(null)null"


  @workflowDescriptor
  Scenario: Test negate attribute. Value: yes
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class" negate="yes" />
    """
    Then  Call a method descriptor "isNegate", I get the value of "(boolean)true"

  @workflowDescriptor
  Scenario: Test negate attribute. Value: true
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class" negate="true" />
    """
    Then  Call a method descriptor "isNegate", I get the value of "(boolean)true"

  @workflowDescriptor
  Scenario: Test negate attribute. Value: abrakadabra
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class" negate="abrakadabra" />
    """
    Then  Call a method descriptor "isNegate", I get the value of "(boolean)false"

  @workflowDescriptor
  Scenario: Create a descriptor from xml.
    Validate save in xml descriptor
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class" id="7" name="test-name" negate="yes">
          <arg name="class.name">TestConditionDescriptorClassName</arg>
          <arg name="testArg">testValue</arg>
      </condition>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <condition type="class" id="7" name="test-name"  negate="true">
          <arg name="class.name">TestConditionDescriptorClassName</arg>
          <arg name="testArg">testValue</arg>
      </condition>
    """

  @workflowDescriptor
  Scenario: Create ConditionDescriptor the type of "phpshell"
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="phpshell" id="1" name="testCondition">
        <arg name="script">echo 'test';</arg>
      </condition>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <condition type="phpshell" id="1" name="testCondition">
        <arg name="script"><![CDATA[echo 'test';]]></arg>
      </condition>
    """

  @workflowDescriptor
  Scenario: Create ConditionDescriptor.
    Attempt to write without reference DOMDocument
    Given Create descriptor "ConditionDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"


  @workflowDescriptor
  Scenario: Create RegisterDescriptor.
    Unknown attribute - type
    Given Create descriptor "ConditionDescriptor"
    Then I save to descriptor xml. I expect to get an exception "\OldTown\Workflow\Exception\InvalidDescriptorException"

  @workflowDescriptor
  Scenario: Create ConditionDescriptor.
    Test negate attribute
    Given Create descriptor "ConditionDescriptor"
    When Call a method descriptor "setNegate". The arguments of the method:
      |negate|
      |true|
    Then Call a method descriptor "isNegate", I get the value of "(boolean)true"


  @workflowDescriptor
  Scenario: Create ConditionDescriptor the type of "class"
    Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class" id="7" name="test-name" negate="yes">
          <arg name="class.name">TestConditionDescriptorClassName</arg>
          <arg name="testArg">testValue</arg>
      </condition>
    """
    Then  Call a method descriptor "validate", I get the value of "(null)null"
