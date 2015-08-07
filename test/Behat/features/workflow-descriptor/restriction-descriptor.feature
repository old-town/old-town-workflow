Feature:Restriction Descriptor


  @workflowDescriptor
  Scenario: Create a descriptor from xml.
  Validate save in xml descriptor
    Given Create descriptor "RestrictionDescriptor" based on xml:
    """
      <restrict-to>
        <conditions type="AND">
          <condition type="class" id="7" name="test-name" negate="yes">
              <arg name="class.name">TestConditionDescriptorClassName</arg>
              <arg name="testArg">testValue</arg>
          </condition>
          <conditions type="OR">
            <condition type="class" id="8" name="test-name2">
                <arg name="class.name">TestConditionDescriptorClassName2</arg>
                <arg name="testArg">testValue2</arg>
            </condition>
              <condition type="phpshell" id="1" name="test-name3">
                  <arg name="script"><![CDATA[echo 'test';]]></arg>
              </condition>
          </conditions>
        </conditions>
      </restrict-to>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <restrict-to>
        <conditions type="AND">
          <condition type="class" id="7" name="test-name" negate="true">
              <arg name="class.name">TestConditionDescriptorClassName</arg>
              <arg name="testArg">testValue</arg>
          </condition>
          <conditions type="OR">
            <condition type="class" id="8" name="test-name2">
                <arg name="class.name">TestConditionDescriptorClassName2</arg>
                <arg name="testArg">testValue2</arg>
            </condition>
              <condition type="phpshell" id="1" name="test-name3">
                  <arg name="script"><![CDATA[echo 'test';]]></arg>
              </condition>
          </conditions>
        </conditions>
      </restrict-to>
    """

  @workflowDescriptor
  Scenario: Create RestrictionDescriptor.
    Attempt to write without reference DOMDocument
    Given Create descriptor "RestrictionDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor. Conditions have not been established
    Given Create descriptor "RestrictionDescriptor" based on xml:
      """
        <restrict-to>
          <conditions type="AND" />
        </restrict-to>
      """
    Then Call a method descriptor "writeXml", I get the value of "(null)null". The arguments of the method:
      |dom|
      |(DOMDocument)domDocument|


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor. Throws an exception in the attached ConditionsDescriptor
    Given Create descriptor "RestrictionDescriptor" based on xml:
        """
          <restrict-to>
            <conditions type="AND">
              <condition type="phpshell" id="1" name="test-name1">
                  <arg name="script"><![CDATA[echo 'test';]]></arg>
              </condition>
              <condition type="phpshell" id="2" name="test-name2">
                  <arg name="script"><![CDATA[echo 'test';]]></arg>
              </condition>
            </conditions>
          </restrict-to>
        """
      And Get the descriptor using the method of "getConditionsDescriptor"
      And Call a method descriptor "setType". The arguments of the method:
        |type|
        |(null)null|
      And Get the descriptor using the method of "getParent"
    Then I save to descriptor xml. I expect to get an exception message "Ошибка сохранения workflow"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test getConditionsDescriptor method. "Conditions Descriptor" is not installed
    Given Create descriptor "RestrictionDescriptor" based on xml:
        """
          <restrict-to />
        """
    Then Call a method descriptor "getConditionsDescriptor", I get the value of "(null)null"

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test validate method. "Conditions Descriptor" is not installed
    Given Create descriptor "RestrictionDescriptor" based on xml:
        """
          <restrict-to />
        """
    Then Call a method descriptor "validate", I get the value of "(null)null"

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Add two nested Conditions. Test validate method.
    Given Create descriptor "RestrictionDescriptor" based on xml:
        """
          <restrict-to>
              <conditions type="AND">

            </conditions>
            <conditions type="AND">

            </conditions>
          </restrict-to>
        """
    Then I validated descriptor. I expect to get an exception message 'Restriction может иметь только один вложенный Condition'