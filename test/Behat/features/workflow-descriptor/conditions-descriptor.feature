Feature:Conditions Descriptor

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor
    Given Create descriptor "ConditionsDescriptor" based on xml:
      """
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
      """
    Then I save to descriptor xml. Compare with xml:
      """
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
      """

  @workflowDescriptor
  Scenario: Create ConditionDescriptor. Save empty descriptor. Test write xml.
    Given Create descriptor "ConditionsDescriptor"
    Then  Call a method descriptor "writeXml", I get the value of "(null)null"

  @workflowDescriptor
  Scenario: Create a descriptor from xml. The type attribute set to null. Save xml.
    Given Create descriptor "ConditionsDescriptor" based on xml:
      """
        <conditions type="AND">
          <condition type="class" id="7" name="test-name" negate="yes">
              <arg name="class.name">TestConditionDescriptorClassName</arg>
              <arg name="testArg">testValue</arg>
          </condition>
          <condition type="phpshell" id="1" name="test-name3">
              <arg name="script"><![CDATA[echo 'test';]]></arg>
          </condition>
        </conditions>
      """
    And Call a method descriptor "setType". The arguments of the method:
      | type       |
      | (null)null |
    Then I save to descriptor xml. I expect to get an exception "\OldTown\Workflow\Exception\InvalidDescriptorException"

  @workflowDescriptor
  Scenario: Create empty  ConditionsDescriptor. Test validate method
    Given Create descriptor "ConditionsDescriptor"
    Then  Call a method descriptor "validate", I get the value of "(null)null"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. A parent is given the name of Action. Validate descriptor.
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
                    <result old-status="Finished" step="2" >
                      <conditions type="AND"/>
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
      And Get the descriptor using the method of "getConditions"
    Then I validated descriptor. I expect to get an exception message "Действие test-action ведущее на шаг step #2 [test-step], должно иметь не менее одного условия в блоке result"

  @workflowDescriptor
  Scenario: Create a descriptor from xml. A parent "action" there is no name.Validate descriptor.
    Given Create descriptor "WorkflowDescriptor" based on xml:
      """
        <workflow id="1">
          <initial-actions>
          </initial-actions>
          <steps>
            <step id="2" name="test-step">
              <actions>
                <action id="3">
                  <results>
                    <result old-status="Finished" step="2" >
                      <conditions type="AND"/>
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
    And Get the descriptor using the method of "getConditions"
    Then I validated descriptor. I expect to get an exception message "Действие OldTown\Workflow\Loader\ActionDescriptor ведущее на шаг step #2 [test-step], должно иметь не менее одного условия в блоке result"



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Do not set the type of conditions. Validate descriptor.
    Given Create descriptor "ConditionsDescriptor" based on xml:
      """
        <conditions type="AND">
          <condition type="phpshell" id="1" name="test-name1">
              <arg name="script"><![CDATA[echo 'test';]]></arg>
          </condition>
          <condition type="phpshell" id="2" name="test-name2">
              <arg name="script"><![CDATA[echo 'test';]]></arg>
          </condition>
        </conditions>
      """
    And Call a method descriptor "setType". The arguments of the method:
      | type       |
      | (null)null |
    Then I validated descriptor. I expect to get an exception message "В условие должен быть определен тип AND или OR"

