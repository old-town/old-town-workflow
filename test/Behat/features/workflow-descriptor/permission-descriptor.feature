Feature:Restriction Descriptor


  @workflowDescriptor
  Scenario: Create a descriptor from xml.
    Validate save in xml descriptor
    Given Create descriptor "PermissionDescriptor" based on xml:
    """
      <permission name="test-permission" id="5">
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
      </permission>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <permission name="test-permission" id="5">
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
      </permission>
    """


  @workflowDescriptor
  Scenario: Create PermissionDescriptor.
    Attempt to write without reference DOMDocument
    Given Create descriptor "PermissionDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"



  @workflowDescriptor
  Scenario: Create a descriptor from xml.
  Validate save in xml descriptor
    Given Create descriptor "PermissionDescriptor" based on xml:
      """
        <permission name="test-permission" />
      """
    Then Call a method descriptor "setName". The arguments of the method:
      |name|
      |(null)null|
    When I save to descriptor xml. I expect to get an exception message "Некорректное значение для атрибута name"