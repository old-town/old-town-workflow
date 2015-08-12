Feature:Conditional Result Descriptor


  @workflowDescriptor
  Scenario: Create a descriptor from xml.
    Validate save in xml descriptor
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
      <result id="10" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
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
        <validators>
            <validator type="class" name="validator-name1" id="50">
              <arg name="class.name">TestValidatorClass</arg>
              <arg name="addInstanceId">true</arg>
            </validator>
            <validator type="phpshell" name="validator-name2" id="60">
                <arg name="script"><![CDATA[echo 'test';]]></arg>
            </validator>
        </validators>
        <pre-functions>
            <function type="class" id="80" name="testFunction">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="90" name="testFunction2">
              <arg name="script">echo 'test';</arg>
            </function>
        </pre-functions>
        <post-functions>
            <function type="class" id="100" name="testFunction3">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="120" name="testFunction4">
              <arg name="script">echo 'test';</arg>
            </function>
        </post-functions>
      </result>
    """
    Then I save to descriptor xml. Compare with xml:
      """
        <result id="10" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
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
          <validators>
              <validator type="class" name="validator-name1" id="50">
                <arg name="class.name">TestValidatorClass</arg>
                <arg name="addInstanceId">true</arg>
              </validator>
              <validator type="phpshell" name="validator-name2" id="60">
                  <arg name="script"><![CDATA[echo 'test';]]></arg>
              </validator>
          </validators>
          <pre-functions>
              <function type="class" id="80" name="testFunction">
                <arg name="class.name">TestClassName</arg>
                <arg name="testArg">testValue</arg>
              </function>
              <function type="phpshell" id="90" name="testFunction2">
                <arg name="script">echo 'test';</arg>
              </function>
          </pre-functions>
          <post-functions>
              <function type="class" id="100" name="testFunction3">
                <arg name="class.name">TestClassName</arg>
                <arg name="testArg">testValue</arg>
              </function>
              <function type="phpshell" id="120" name="testFunction4">
                <arg name="script">echo 'test';</arg>
              </function>
          </post-functions>
        </result>
      """

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test split attribute. Validate save in xml descriptor
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
        <result old-status="Finished" split="2"/>
    """
    Then I save to descriptor xml. Compare with xml:
    """
        <result old-status="Finished" split="2"/>
    """

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test join attribute. Validate save in xml descriptor
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
        <result old-status="Finished" join="1"/>
    """
    Then I save to descriptor xml. Compare with xml:
    """
        <result old-status="Finished" join="1"/>
    """


  @workflowDescriptor
  Scenario: Create ResultDescriptor. Attempt to write without reference DOMDocument
    Given Create descriptor "ConditionalResultDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking the preservation of the descriptor in the xml, the incorrect attribute of old-status.
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
      <result old-status="Finished" />
    """
    And Call a method descriptor "setOldStatus". The arguments of the method:
      |oldStatus|
      |(null)null|
    Then I save to descriptor xml. I expect to get an exception message "Некорректное значение для атрибута old-status"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking the preservation of the descriptor in the xml, the incorrect attribute of status.
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
      <result old-status="Finished"  />
    """
    Then I save to descriptor xml. I expect to get an exception message "Некорректное значение для атрибута status"



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking the preservation of the descriptor in the xml, the incorrect attribute of status.
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
      <result old-status="Finished" status="Queued" />
    """
    Then I save to descriptor xml. I expect to get an exception message "Некорректное значение для атрибута step"