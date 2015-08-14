Feature: Action Descriptor

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validation save in xml descriptor.
    Given Create descriptor "ActionDescriptor" based on xml:
    """
      <action id="10" name="Start Workflow" view="test-view" auto="true" finish="true">
        <meta name="lastModified">Sun Dec 17 16:57:01 ART 2006</meta>
        <meta name="created">Sun Dec 17 16:55:59 ART 2006</meta>
        <meta name="generator">OSWOrkflow Designer</meta>

        <restrict-to>
          <conditions type="AND">
            <condition type="class" id="20" name="test-name" negate="true">
                <arg name="class.name">TestConditionDescriptorClassName</arg>
                <arg name="testArg">testValue</arg>
            </condition>
            <conditions type="OR">
              <condition type="class" id="30" name="test-name2">
                  <arg name="class.name">TestConditionDescriptorClassName2</arg>
                  <arg name="testArg">testValue2</arg>
              </condition>
                <condition type="phpshell" id="40" name="test-name3">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </condition>
            </conditions>
          </conditions>
        </restrict-to>

        <validators>
          <validator type="class" name="validator-name" id="50">
            <arg name="class.name">TestValidatorClass1</arg>
            <arg name="addInstanceId">true</arg>
          </validator>
          <validator type="class" name="validator-name" id="60">
            <arg name="class.name">TestValidatorClass2</arg>
            <arg name="addInstanceId">true</arg>
          </validator>
        </validators>

        <pre-functions>
            <function type="class" id="70" name="testFunction1">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="80" name="testFunction2">
              <arg name="script">echo 'test';</arg>
            </function>
        </pre-functions>

        <results>
          <result id="90" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
            <conditions type="AND">
            <condition type="class" id="100" name="test-name" negate="true">
                <arg name="class.name">TestConditionDescriptorClassName</arg>
                <arg name="testArg">testValue</arg>
            </condition>
            <conditions type="OR">
                <condition type="class" id="110" name="test-name2">
                    <arg name="class.name">TestConditionDescriptorClassName2</arg>
                    <arg name="testArg">testValue2</arg>
                </condition>
                  <condition type="phpshell" id="120" name="test-name3">
                      <arg name="script"><![CDATA[echo 'test';]]></arg>
                  </condition>
              </conditions>
            </conditions>
            <validators>
                <validator type="class" name="validator-name1" id="130">
                  <arg name="class.name">TestValidatorClass</arg>
                  <arg name="addInstanceId">true</arg>
                </validator>
                <validator type="phpshell" name="validator-name2" id="140">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </validator>
            </validators>
            <pre-functions>
                <function type="class" id="150" name="testFunction3">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="160" name="testFunction4">
                  <arg name="script">echo 'test';</arg>
                </function>
            </pre-functions>
            <post-functions>
                <function type="class" id="170" name="testFunction5">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="180" name="testFunction6">
                  <arg name="script">echo 'test';</arg>
                </function>
            </post-functions>
          </result>
          <unconditional-result id="190" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
            <validators>
                <validator type="class" name="validator-name1" id="200">
                  <arg name="class.name">TestValidatorClass</arg>
                  <arg name="addInstanceId">true</arg>
                </validator>
                <validator type="phpshell" name="validator-name2" id="210">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </validator>
            </validators>
            <pre-functions>
                <function type="class" id="220" name="testFunction7">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="230" name="testFunction8">
                  <arg name="script">echo 'test';</arg>
                </function>
            </pre-functions>
            <post-functions>
                <function type="class" id="240" name="testFunction9">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="250" name="testFunction10">
                  <arg name="script">echo 'test';</arg>
                </function>
            </post-functions>
          </unconditional-result>
        </results>
        <post-functions>
            <function type="class" id="260" name="testFunction11">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="270" name="testFunction12">
              <arg name="script">echo 'test';</arg>
            </function>
        </post-functions>
      </action>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <action id="10" name="Start Workflow" view="test-view" auto="true" finish="true">
        <meta name="lastModified">Sun Dec 17 16:57:01 ART 2006</meta>
        <meta name="created">Sun Dec 17 16:55:59 ART 2006</meta>
        <meta name="generator">OSWOrkflow Designer</meta>

        <restrict-to>
          <conditions type="AND">
            <condition type="class" id="20" name="test-name" negate="true">
                <arg name="class.name">TestConditionDescriptorClassName</arg>
                <arg name="testArg">testValue</arg>
            </condition>
            <conditions type="OR">
              <condition type="class" id="30" name="test-name2">
                  <arg name="class.name">TestConditionDescriptorClassName2</arg>
                  <arg name="testArg">testValue2</arg>
              </condition>
                <condition type="phpshell" id="40" name="test-name3">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </condition>
            </conditions>
          </conditions>
        </restrict-to>

        <validators>
          <validator type="class" name="validator-name" id="50">
            <arg name="class.name">TestValidatorClass1</arg>
            <arg name="addInstanceId">true</arg>
          </validator>
          <validator type="class" name="validator-name" id="60">
            <arg name="class.name">TestValidatorClass2</arg>
            <arg name="addInstanceId">true</arg>
          </validator>
        </validators>

        <pre-functions>
            <function type="class" id="70" name="testFunction1">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="80" name="testFunction2">
              <arg name="script">echo 'test';</arg>
            </function>
        </pre-functions>

        <results>
          <result id="90" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
            <conditions type="AND">
            <condition type="class" id="100" name="test-name" negate="true">
                <arg name="class.name">TestConditionDescriptorClassName</arg>
                <arg name="testArg">testValue</arg>
            </condition>
            <conditions type="OR">
                <condition type="class" id="110" name="test-name2">
                    <arg name="class.name">TestConditionDescriptorClassName2</arg>
                    <arg name="testArg">testValue2</arg>
                </condition>
                  <condition type="phpshell" id="120" name="test-name3">
                      <arg name="script"><![CDATA[echo 'test';]]></arg>
                  </condition>
              </conditions>
            </conditions>
            <validators>
                <validator type="class" name="validator-name1" id="130">
                  <arg name="class.name">TestValidatorClass</arg>
                  <arg name="addInstanceId">true</arg>
                </validator>
                <validator type="phpshell" name="validator-name2" id="140">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </validator>
            </validators>
            <pre-functions>
                <function type="class" id="150" name="testFunction3">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="160" name="testFunction4">
                  <arg name="script">echo 'test';</arg>
                </function>
            </pre-functions>
            <post-functions>
                <function type="class" id="170" name="testFunction5">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="180" name="testFunction6">
                  <arg name="script">echo 'test';</arg>
                </function>
            </post-functions>
          </result>
          <unconditional-result id="190" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
            <validators>
                <validator type="class" name="validator-name1" id="200">
                  <arg name="class.name">TestValidatorClass</arg>
                  <arg name="addInstanceId">true</arg>
                </validator>
                <validator type="phpshell" name="validator-name2" id="210">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </validator>
            </validators>
            <pre-functions>
                <function type="class" id="220" name="testFunction7">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="230" name="testFunction8">
                  <arg name="script">echo 'test';</arg>
                </function>
            </pre-functions>
            <post-functions>
                <function type="class" id="240" name="testFunction9">
                  <arg name="class.name">TestClassName</arg>
                  <arg name="testArg">testValue</arg>
                </function>
                <function type="phpshell" id="250" name="testFunction10">
                  <arg name="script">echo 'test';</arg>
                </function>
            </post-functions>
          </unconditional-result>
        </results>
        <post-functions>
            <function type="class" id="260" name="testFunction11">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="270" name="testFunction12">
              <arg name="script">echo 'test';</arg>
            </function>
        </post-functions>
      </action>
    """


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validation save in xml descriptor.
    Checking for exceptions when a descriptor is created. No tag "results"
    When Create descriptor "ActionDescriptor" based on xml. I expect exception with the text "Отсутствует обязательный блок results". Xml source:
    """
      <action id="10" name="Start Workflow" view="test-view" auto="true" finish="true" />
    """


  @workflowDescriptor
  Scenario: Create ResultDescriptor. Attempt to write without reference DOMDocument
    Given Create descriptor "ActionDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Не передан DOMDocument"


  @workflowDescriptor
  Scenario: Check that there is an exception if ActionDescriptor saved without id
    Given Create descriptor "ActionDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Отсутствует атрибут id". The arguments of the method:
      |dom|
      |(DOMDocument)domDocument|


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test validate method
    Given Create descriptor "ActionDescriptor" based on xml:
    """
      <action id="10">
        <restrict-to>
            <conditions type="AND">
                <condition type="phpshell" id="40" name="test-name3">
                    <arg name="script"><![CDATA[echo 'test';]]></arg>
                </condition>
            </conditions>
        </restrict-to>
        <results>
          <result id="90" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
            <conditions type="AND">
              <condition type="phpshell" id="120" name="test-name3">
                  <arg name="script"><![CDATA[echo 'test';]]></arg>
              </condition>
            </conditions>
          </result>
          <unconditional-result id="190" old-status="Finished" status="Queued" step="2" />
        </results>
      </action>
    """
    Then Call a method descriptor "validate", I get the value of "(null)null"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test validate method
    Given Create descriptor "ActionDescriptor" based on xml:
    """
      <action id="10" name="test">
        <results>
          <result id="90" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
            <conditions type="AND">
              <condition type="phpshell" id="120" name="test-name3">
                  <arg name="script"><![CDATA[echo 'test';]]></arg>
              </condition>
            </conditions>
          </result>
        </results>
      </action>
    """
    Then I validated descriptor. I expect to get an exception message "Действие test имеет условные условия, но не имеет запасного безусловного"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing common action.
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
        <workflow id="1">
          <initial-actions>
          </initial-actions>
          <steps>
          </steps>
          <common-actions>
            <action id="10" name="test">
              <results>
                <unconditional-result id="190" old-status="Finished" status="Queued" step="2" />
              </results>
            </action>
          </common-actions>
        </workflow>
    """
    And Get the descriptor using the method of "getCommonActions"
    Then Call a method descriptor "isCommon", I get the value of "(boolean)true"
