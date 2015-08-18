Feature:Step Descriptor


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <common-actions>
          <action id="4" name="test-action-name4">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-4"/>
            </results>
          </action>
          <action id="7" name="test-action-name7">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-7"/>
            </results>
          </action>
        </common-actions>
        <steps>
          <step id="10" name="test-name">
            <meta name="lastModified">Sun Dec 17 16:57:01 ART 2006</meta>
            <meta name="created">Sun Dec 17 16:55:59 ART 2006</meta>
            <meta name="generator">OSWOrkflow Designer</meta>
            <pre-functions>
                <function type="class" id="20" name="testFunction10">
                  <arg name="class.name">TestClassName10</arg>
                  <arg name="testArg">testValue10</arg>
                </function>
                <function type="phpshell" id="30" name="testFunction20">
                  <arg name="script">echo 'test10';</arg>
                </function>
            </pre-functions>
            <external-permissions>
              <permission name="test-permission" id="40">
                <restrict-to>
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
                </restrict-to>
              </permission>
            </external-permissions>
            <actions>
              <common-action id="4"/>
              <common-action id="7"/>
              <action id="80" name="test-action-name60">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-10"/>
                </results>
              </action>
              <action id="90" name="test-action-name70">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-20"/>
                </results>
              </action>
            </actions>
            <post-functions>
                <function type="class" id="100" name="testFunction80">
                  <arg name="class.name">TestClassName50</arg>
                  <arg name="testArg">testValue60</arg>
                </function>
                <function type="phpshell" id="110" name="testFunction90">
                  <arg name="script">echo 'test70';</arg>
                </function>
            </post-functions>
          </step>
        </steps>
      </workflow>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <workflow>
        <initial-actions />
        <common-actions>
          <action id="4" name="test-action-name4">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-4"/>
            </results>
          </action>
          <action id="7" name="test-action-name7">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-7"/>
            </results>
          </action>
        </common-actions>
        <steps>
          <step id="10" name="test-name">
            <meta name="lastModified">Sun Dec 17 16:57:01 ART 2006</meta>
            <meta name="created">Sun Dec 17 16:55:59 ART 2006</meta>
            <meta name="generator">OSWOrkflow Designer</meta>
            <pre-functions>
                <function type="class" id="20" name="testFunction10">
                  <arg name="class.name">TestClassName10</arg>
                  <arg name="testArg">testValue10</arg>
                </function>
                <function type="phpshell" id="30" name="testFunction20">
                  <arg name="script">echo 'test10';</arg>
                </function>
            </pre-functions>
            <external-permissions>
              <permission name="test-permission" id="40">
                <restrict-to>
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
                </restrict-to>
              </permission>
            </external-permissions>
            <actions>
              <common-action id="4"/>
              <common-action id="7"/>
              <action id="80" name="test-action-name60">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-10"/>
                </results>
              </action>
              <action id="90" name="test-action-name70">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-20"/>
                </results>
              </action>
            </actions>
            <post-functions>
                <function type="class" id="100" name="testFunction80">
                  <arg name="class.name">TestClassName50</arg>
                  <arg name="testArg">testValue60</arg>
                </function>
                <function type="phpshell" id="110" name="testFunction90">
                  <arg name="script">echo 'test70';</arg>
                </function>
            </post-functions>
          </step>
        </steps>
      </workflow>
    """


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor
    Then Create descriptor "StepDescriptor" based on xml. I expect exception with the text "Отсутствует Workflow Descriptor". Xml source:
    """
      <step id="10" name="test-name">
        <actions>
          <common-action id="4"/>
          <common-action id="7"/>
          <action id="80" name="test-action-name60">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-10"/>
            </results>
          </action>
          <action id="90" name="test-action-name70">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-20"/>
            </results>
          </action>
        </actions>
      </step>
    """


  @workflowDescriptor
  Scenario: Create StepDescriptor. Attempt to write without reference DOMDocument
    Given Create descriptor "StepDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Не передан DOMDocument"


  @workflowDescriptor
  Scenario: Create StepDescriptor. Do not set the id attribute.
    Given Create descriptor "StepDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Отсутствует атрибут id". The arguments of the method:
      |dom|
      |(DOMDocument)domDocument|


  @workflowDescriptor
  Scenario: Create a descriptor from xml. We get the "ActionDescripor" by id.
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions>
              <common-action id="4"/>
              <common-action id="7"/>
              <action id="80" name="test-action-name60">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-10"/>
                </results>
              </action>
              <action id="90" name="test-action-name70">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-20"/>
                </results>
              </action>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    When Get the descriptor using the method of "getAction". The arguments of the method:
      |id|
      |80|
    Then Call a method descriptor "getName", I get the value of "test-action-name60"


  @workflowDescriptor
  Scenario: Create a descriptor from xml.Testing method of "getActionDescripor".Id is invalid.
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions>
              <common-action id="4"/>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    Then Call a method descriptor "getAction", I get the value of "(null)null". The arguments of the method:
      |id|
      |99|


  @workflowDescriptor
  Scenario: Create a descriptor from xml.Test the removal of all actions
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions>
              <action id="80" name="test-action-name60">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-10"/>
                </results>
              </action>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    When Call a method descriptor "getFlagHasActions", I get the value of "(boolean)true"
    When Call a method descriptor "removeActions"
    Then Call a method descriptor "getFlagHasActions", I get the value of "(boolean)false"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Testing method of "resultsInJoin". Invalid argument value.
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions>
              <action id="80" name="test-action-name60">
                <results>
                  <unconditional-result old-status="Finished" step="10" status="Queued-10"/>
                </results>
              </action>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    When Call a method descriptor "resultsInJoin". I expect to get an exception message "Аргумент должен быть числом". The arguments of the method:
      |join|
      |text|


  @workflowDescriptor
  Scenario: Create a descriptor from xml.Testing method of "resultsInJoin". The value of attribute "join" in the absolute results
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions>
              <action id="80" name="test-action-name60">
                <results>
                    <result old-status="Finished" join="12">
                      <conditions type="AND">
                        <condition type="class" id="8" name="test-name" negate="true">
                            <arg name="class.name">TestConditionDescriptorClassName</arg>
                            <arg name="testArg">testValue</arg>
                        </condition>
                      </conditions>
                    </result>
                  <unconditional-result old-status="Finished" join="7"/>
                </results>
              </action>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    When Call a method descriptor "resultsInJoin", I get the value of "(boolean)true". The arguments of the method:
      |join|
      |7|


  @workflowDescriptor
  Scenario: Create a descriptor from xml.Testing method of "resultsInJoin". Testing method of "resultsInJoin".
    The value of attribute "join" in the result with the condition
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions>
              <action id="80" name="test-action-name60">
                <results>
                    <result old-status="Finished" join="12">
                      <conditions type="AND">
                        <condition type="class" id="8" name="test-name" negate="true">
                            <arg name="class.name">TestConditionDescriptorClassName</arg>
                            <arg name="testArg">testValue</arg>
                        </condition>
                      </conditions>
                    </result>
                  <unconditional-result old-status="Finished" join="7"/>
                </results>
              </action>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    When Call a method descriptor "resultsInJoin", I get the value of "(boolean)true". The arguments of the method:
      |join|
      |12|


  @workflowDescriptor
  Scenario: Create a descriptor from xml.Testing method of "resultsInJoin". Testing method of "resultsInJoin".
  Testing method of "resultsInJoin". A nonexistent value
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions>
              <action id="80" name="test-action-name60">
                <results>
                    <result old-status="Finished" join="12">
                      <conditions type="AND">
                        <condition type="class" id="8" name="test-name" negate="true">
                            <arg name="class.name">TestConditionDescriptorClassName</arg>
                            <arg name="testArg">testValue</arg>
                        </condition>
                      </conditions>
                    </result>
                  <unconditional-result old-status="Finished" join="7"/>
                </results>
              </action>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    When Call a method descriptor "resultsInJoin", I get the value of "(boolean)false". The arguments of the method:
      |join|
      |99|


  @workflowDescriptor
  Scenario: Create a descriptor from xml.Testing method of "validate". No actions
  Testing method of "resultsInJoin". A nonexistent value
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="10" name="test-name">
            <actions/>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    Then I validated descriptor. I expect to get an exception message "Шаг test-name должен содержать одни действие или одно общее действие"


  @workflowDescriptor
  Scenario: Create a descriptor from xml.Testing method of "validate". Invalid step id.
  Testing method of "resultsInJoin". A nonexistent value
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <steps>
          <step id="-1" name="test-name">
            <actions>
              <action id="80" name="test-action-name60">
                <results>
                  <unconditional-result old-status="Finished" join="7"/>
                </results>
              </action>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    Then I validated descriptor. I expect to get an exception message "В качестве id шага нельзя использовать -1, так как это зарезериврованное значение"


  @workflowDescriptor
  Scenario: Create StepDescriptor. No WorkflowDescriptor
    Given Create descriptor "StepDescriptor"
    Then I validated descriptor. I expect to get an exception message "Родительский элемент для шага должен реализовывать OldTown\Workflow\Loader\WorkflowDescriptor"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <common-actions>
          <action id="4" name="test-action-name4">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-4"/>
            </results>
          </action>
          <action id="7" name="test-action-name7">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-7"/>
            </results>
          </action>
        </common-actions>
        <steps>
          <step id="10" name="test-name">
            <actions>
              <common-action id="4"/>
              <common-action id="10"/>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    Then I validated descriptor. I expect to get an exception message "Некорректный id для common-action: id 10"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor
    Given Create descriptor "WorkflowDescriptor" based on xml:
    """
      <workflow>
        <initial-actions />
        <common-actions>
          <action id="4" name="test-action-name4">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-4"/>
            </results>
          </action>
          <action id="7" name="test-action-name7">
            <results>
              <unconditional-result old-status="Finished" step="10" status="Queued-7"/>
            </results>
          </action>
        </common-actions>
        <steps>
          <step id="10" name="test-name">
            <actions>
              <common-action id="4"/>
              <common-action id="7"/>
            </actions>
          </step>
        </steps>
      </workflow>
    """
    And Get the descriptor using the method of "getSteps"
    Then Call a method descriptor "validate"