Feature:Default Variable Resolver


  Scenario: Test VariableResolver. Resolve value from TransientVars. Test getter/setter
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <registers>
        <register type="class.name" variable-name="dateObj">
            <arg name="class.name">\OldTownWorkflowBehatTestData\VariableResolver\Register</arg>
        </register>
    </registers>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <validators>
                <validator type="class.name">
                    <arg name="class.name">OldTownWorkflowBehatTestData\VariableResolver\Validator</arg>
                    <arg name="expected">${dateObj.value1.value2}</arg>
                    <arg name="actual">value_2</arg>
                </validator>
            </validators>
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="2"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="2" name="First Draft">
            <actions>
                <action id="811" name="Finish_First_Draft">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="2"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
    And Create workflow manager
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
    Then Process of workflow with the alias "test" has the below steps:
      |stepId|
      |2     |
    And Exceptions are missing



  Scenario: Test VariableResolver. Test object convert to string
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <registers>
        <register type="class.name" variable-name="dateObj">
            <arg name="class.name">\OldTownWorkflowBehatTestData\VariableResolver\Register</arg>
        </register>
    </registers>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <validators>
                <validator type="class.name">
                    <arg name="class.name">OldTownWorkflowBehatTestData\VariableResolver\Validator</arg>
                    <arg name="expected"> ${dateObj} </arg>
                    <arg name="actual"> test_string_value </arg>
                </validator>
            </validators>
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="2"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="2" name="First Draft">
            <actions>
                <action id="811" name="Finish_First_Draft">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="2"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
    And Create workflow manager
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
    Then Process of workflow with the alias "test" has the below steps:
      |stepId|
      |2     |
    And Exceptions are missing
