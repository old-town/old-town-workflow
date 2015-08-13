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


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method getDestination. No parent descriptor.
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
      <result old-status="Finished" status="Queued" />
    """
    Then Call a method descriptor "getDestination". I expect to get an exception message "Родитель должен реализовывать OldTown\Workflow\Loader\AbstractDescriptor"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method getDestination. In result of these non-existent stepId.
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
        <workflow id="1">
          <initial-actions>
          </initial-actions>
          <steps>
            <step id="2" name="test-step">
              <actions>
                <action id="3" name="test-action">
                  <results>
                    <result old-status="Finished" step="7" />
                  </results>
                </action>
              </actions>
            </step>
          </steps>
        </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    And Get the descriptor using the method of "getActions"
    And Get the descriptor using the method of "getConditionalResults"
    Then Call a method descriptor "getDestination". I expect to get an exception message "Дескриптор шалаг должен реализовывать OldTown\Workflow\Loader\StepDescriptor"



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method getDestination. For step.
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
        <workflow id="1">
          <initial-actions>
          </initial-actions>
          <steps>
            <step id="7" name="test-step">
              <actions>
                <action id="3" name="test-action">
                  <results>
                    <result old-status="Finished" step="7" />
                  </results>
                </action>
              </actions>
            </step>
          </steps>
        </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    And Get the descriptor using the method of "getActions"
    And Get the descriptor using the method of "getConditionalResults"
    Then Call a method descriptor "getDestination", I get the value of "step #7 [test-step]"



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method getDestination. For join.
    Given Create descriptor "ActionDescriptor" based on xml:
    """
      <action id="3" name="test-action">
        <results>
          <result old-status="Finished" join="7" />
        </results>
      </action>
    """
    And Get the descriptor using the method of "getConditionalResults"
    Then Call a method descriptor "getDestination", I get the value of "join #7"



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method getDestination. For split.
    Given Create descriptor "ActionDescriptor" based on xml:
    """
      <action id="3" name="test-action">
        <results>
          <result old-status="Finished" split="7" />
        </results>
      </action>
    """
    And Get the descriptor using the method of "getConditionalResults"
    Then Call a method descriptor "getDestination", I get the value of "split #7"



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method validate.
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
        <workflow id="1">
          <initial-actions>
          </initial-actions>
          <steps>
            <step id="7" name="test-step">
              <actions>
                <action id="3" name="test-action">
                  <results>
                    <result old-status="Finished" step="7" status="Queued">
                      <conditions type="AND">
                        <condition type="class" id="8" name="test-name" negate="true">
                            <arg name="class.name">TestConditionDescriptorClassName</arg>
                            <arg name="testArg">testValue</arg>
                        </condition>
                      </conditions>
                    </result>
                  </results>
                </action>
              </actions>
            </step>
          </steps>
        </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    And Get the descriptor using the method of "getActions"
    And Get the descriptor using the method of "getConditionalResults"
    Then Call a method descriptor "validate", I get the value of "(null)null"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method validate. No parent descriptor.
    Given Create descriptor "ConditionalResultDescriptor" based on xml:
    """
      <result old-status="Finished" step="7" status="Queued" />
    """
    Then I validated descriptor. I expect to get an exception message "Родитель должен реализовывать OldTown\Workflow\Loader\ActionDescriptor"




  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method validate. No parent descriptor.
    Given Create descriptor "ActionDescriptor" based on xml:
    """
      <action id="3" name="test-action">
        <results>
          <result old-status="Finished" split="7" />
        </results>
      </action>
    """
    And Get the descriptor using the method of "getConditionalResults"
    Then I validated descriptor. I expect to get an exception message "Результат условия от test-action к split #7 должны иметь по крайней мере одну условие"