Feature:Workflow Descriptor

@workflowDescriptor
Scenario: Create a descriptor from xml. Validate save in xml descriptor
Given Create descriptor "WorkflowDescriptor" based on xml:
"""
      <workflow>
        <meta name="lastModified">Sun Dec 17 16:57:01 ART 2006</meta>
        <meta name="created">Sun Dec 17 16:55:59 ART 2006</meta>
        <meta name="generator">OSWOrkflow Designer</meta>

        <registers>
          <register type="class" variable-name="log" id="10">
              <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
              <arg name="addInstanceId">true</arg>
          </register>
          <register type="phpshell" variable-name="value10" id="20">
            <arg name="script">echo 'test10';</arg>
          </register>
        </registers>

        <trigger-functions>
          <trigger-function id="30">
            <function type="class" id="40" name="value20">
              <arg name="class.name">value30</arg>
              <arg name="testArg">value40</arg>
            </function>
          </trigger-function>
          <trigger-function id="50">
            <function type="phpshell" id="60" name="value50">
              <arg name="script">echo 'test20';</arg>
            </function>
          </trigger-function>
          <trigger-function id="70">
            <function type="class" id="80" name="value60">
              <arg name="class.name">value70</arg>
              <arg name="testArg">value80</arg>
            </function>
          </trigger-function>
        </trigger-functions>

        <global-conditions>
          <conditions type="OR">

            <conditions type="AND">
              <condition type="phpshell" id="90" name="value90">
                  <arg name="script"><![CDATA[echo 'test30';]]></arg>
              </condition>
              <condition type="class" id="100" name="test-name"  negate="true">
                  <arg name="class.name">value100</arg>
                  <arg name="testArg">value110</arg>
              </condition>
            </conditions>
            <conditions type="OR">
              <condition type="phpshell" id="110" name="value120">
                  <arg name="script"><![CDATA[echo 'test50';]]></arg>
              </condition>
              <condition type="phpshell" id="120" name="130">
                  <arg name="script"><![CDATA[echo 'test60';]]></arg>
              </condition>
            </conditions>

          </conditions>
        </global-conditions>


        <initial-actions>
          <action id="150" name="Start Workflow">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
          <action id="160" name="Start Workflow - version2">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
        </initial-actions>

      <global-actions>
          <action id="153" name="test-global-actions-1">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
          <action id="157" name="test-global-actions-2">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
      </global-actions>


        <common-actions>
          <action id="170" name="common-action-1">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="200" />
            </results>
          </action>
          <action id="180" name="common-action-2">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="200" />
            </results>
          </action>
        </common-actions>


        <steps>
          <step id="190" name="step-1">
            <actions>
              <common-action id="180"/>
            </actions>
          </step>
          <step id="200" name="step-2">
            <actions>
              <common-action id="170"/>
              <action id="210" name="test-split-action">
                <results>
                  <unconditional-result old-status="Finished" split="2"/>
                </results>
              </action>
            </actions>
          </step>
          <step id="220" name="step-3">
            <actions>
              <action id="230" name="test-join-action-1">
                <results>
                  <unconditional-result old-status="Finished" join="280"/>
                </results>
              </action>
            </actions>
          </step>
          <step id="240" name="step-4">
            <actions>
              <action id="250" name="test-join-action-2">
                <results>
                  <unconditional-result old-status="Finished" join="280"/>
                </results>
              </action>
            </actions>
          </step>
          <step id="260" name="step-5">
            <actions>
              <action id="270" name="finish-action" >
                <results>
                  <unconditional-result old-status="Finished" status="Finished" step="270"/>
                </results>
              </action>
            </actions>
          </step>

        </steps>

        <splits>
            <split id="280">
                <unconditional-result old-status="Finished" status="Underway"  step="220"/>
                <unconditional-result old-status="Finished" status="Underway" step="230"/>
            </split>
        </splits>

        <joins>
            <join id="290">
                <unconditional-result old-status="Finished" status="Underway"  step="260"/>
            </join>
        </joins>

      </workflow>
    """
  Then I save to descriptor xml. Compare with xml:
  """
      <workflow>
        <meta name="lastModified">Sun Dec 17 16:57:01 ART 2006</meta>
        <meta name="created">Sun Dec 17 16:55:59 ART 2006</meta>
        <meta name="generator">OSWOrkflow Designer</meta>

        <registers>
          <register type="class" variable-name="log" id="10">
              <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
              <arg name="addInstanceId">true</arg>
          </register>
          <register type="phpshell" variable-name="value10" id="20">
            <arg name="script">echo 'test10';</arg>
          </register>
        </registers>

        <trigger-functions>
          <trigger-function id="30">
            <function type="class" id="40" name="value20">
              <arg name="class.name">value30</arg>
              <arg name="testArg">value40</arg>
            </function>
          </trigger-function>
          <trigger-function id="50">
            <function type="phpshell" id="60" name="value50">
              <arg name="script">echo 'test20';</arg>
            </function>
          </trigger-function>
          <trigger-function id="70">
            <function type="class" id="80" name="value60">
              <arg name="class.name">value70</arg>
              <arg name="testArg">value80</arg>
            </function>
          </trigger-function>
        </trigger-functions>

        <global-conditions>
          <conditions type="OR">

            <conditions type="AND">
              <condition type="phpshell" id="90" name="value90">
                  <arg name="script"><![CDATA[echo 'test30';]]></arg>
              </condition>
              <condition type="class" id="100" name="test-name"  negate="true">
                  <arg name="class.name">value100</arg>
                  <arg name="testArg">value110</arg>
              </condition>
            </conditions>
            <conditions type="OR">
              <condition type="phpshell" id="110" name="value120">
                  <arg name="script"><![CDATA[echo 'test50';]]></arg>
              </condition>
              <condition type="phpshell" id="120" name="130">
                  <arg name="script"><![CDATA[echo 'test60';]]></arg>
              </condition>
            </conditions>

          </conditions>
        </global-conditions>


        <initial-actions>
          <action id="150" name="Start Workflow">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
          <action id="160" name="Start Workflow - version2">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
        </initial-actions>

      <global-actions>
          <action id="153" name="test-global-actions-1">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
          <action id="157" name="test-global-actions-2">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="190" />
            </results>
          </action>
      </global-actions>

        <common-actions>
          <action id="170" name="common-action-1">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="200" />
            </results>
          </action>
          <action id="180" name="common-action-2">
            <results>
              <unconditional-result old-status="Finished" status="Underway" step="200" />
            </results>
          </action>
        </common-actions>


        <steps>
          <step id="190" name="step-1">
            <actions>
              <common-action id="180"/>
            </actions>
          </step>
          <step id="200" name="step-2">
            <actions>
              <common-action id="170"/>
              <action id="210" name="test-split-action">
                <results>
                  <unconditional-result old-status="Finished" split="2"/>
                </results>
              </action>
            </actions>
          </step>
          <step id="220" name="step-3">
            <actions>
              <action id="230" name="test-join-action-1">
                <results>
                  <unconditional-result old-status="Finished" join="280"/>
                </results>
              </action>
            </actions>
          </step>
          <step id="240" name="step-4">
            <actions>
              <action id="250" name="test-join-action-2">
                <results>
                  <unconditional-result old-status="Finished" join="280"/>
                </results>
              </action>
            </actions>
          </step>
          <step id="260" name="step-5">
            <actions>
              <action id="270" name="finish-action" >
                <results>
                  <unconditional-result old-status="Finished" status="Finished" step="270"/>
                </results>
              </action>
            </actions>
          </step>

        </steps>

        <splits>
            <split id="280">
                <unconditional-result old-status="Finished" status="Underway"  step="220"/>
                <unconditional-result old-status="Finished" status="Underway" step="230"/>
            </split>
        </splits>

        <joins>
            <join id="290">
                <unconditional-result old-status="Finished" status="Underway"  step="260"/>
            </join>
        </joins>

      </workflow>
   """


  @workflowDescriptor
  Scenario: Create empty WorkflowDescriptor. Test writeXml
    Given Create descriptor "WorkflowDescriptor"
    Then I save to descriptor xml. Not DomDocument. Compare with xml:
    """
    <?xml version="1.0"?>
    <workflow>
      <initial-actions/>
      <steps/>
    </workflow>
    """


  @workflowDescriptor
  Scenario: Create empty WorkflowDescriptor. Test writeXml
    Given Create descriptor "WorkflowDescriptor"
    When Call a method descriptor "setName". The arguments of the method:
      |workflowName|
      |test        |
    Then Call a method descriptor "getName", I get the value of "test"