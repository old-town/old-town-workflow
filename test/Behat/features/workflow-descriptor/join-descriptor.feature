Feature: Join Descriptor



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor
    Given Create descriptor "JoinDescriptor" based on xml:
    """
      <join id="1">
          <conditions type="AND">
            <condition type="class" id="50" name="test-name30" negate="true">
                <arg name="class.name">TestClassName20</arg>
                <arg name="testArg">testValue20</arg>
            </condition>
            <conditions type="OR">
              <condition type="class" id="60" name="test-name40">
                  <arg name="class.name">TestClassName30</arg>
                  <arg name="testArg">TestClassName40</arg>
              </condition>
                <condition type="phpshell" id="70" name="test-name50">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </condition>
            </conditions>
          </conditions>
          <unconditional-result old-status="Finished" status="Underway" owner="test" step="2"/>
      </join>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <join id="1">
          <conditions type="AND">
            <condition type="class" id="50" name="test-name30" negate="true">
                <arg name="class.name">TestClassName20</arg>
                <arg name="testArg">testValue20</arg>
            </condition>
            <conditions type="OR">
              <condition type="class" id="60" name="test-name40">
                  <arg name="class.name">TestClassName30</arg>
                  <arg name="testArg">TestClassName40</arg>
              </condition>
                <condition type="phpshell" id="70" name="test-name50">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </condition>
            </conditions>
          </conditions>
          <unconditional-result old-status="Finished" status="Underway" owner="test" step="2"/>
      </join>
    """


  @workflowDescriptor
  Scenario: Create JoinDescriptor. Attempt to write without reference DOMDocument
    Given Create descriptor "JoinDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Не передан DOMDocument"


  @workflowDescriptor
  Scenario: Create JoinDescriptor. Do not set the id attribute.
    Given Create descriptor "JoinDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Отсутствует атрибут id". The arguments of the method:
      |dom|
      |(DOMDocument)domDocument|


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test validate method. Valid descriptor.
    Given Create descriptor "JoinDescriptor" based on xml:
    """
      <join id="1">
          <conditions type="AND">
            <condition type="phpshell" id="70" name="test-name50">
              <arg name="script"><![CDATA[echo 'test';]]></arg>
            </condition>
          </conditions>
          <unconditional-result old-status="Finished" status="Underway" owner="test" step="2"/>
      </join>
    """
    When Call a method descriptor "validate", I get the value of "(null)null"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test validate method. Valid descriptor. No tag unconditional-result
    Given Create descriptor "JoinDescriptor" based on xml:
    """
      <join id="1">
          <conditions type="AND">
            <condition type="phpshell" id="70" name="test-name50">
              <arg name="script"><![CDATA[echo 'test';]]></arg>
            </condition>
          </conditions>
      </join>
    """
    When I validated descriptor. I expect to get an exception message "Join должен иметь реузультат"