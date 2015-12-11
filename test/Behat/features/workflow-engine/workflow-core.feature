Feature:Workflow Core


  Scenario: initialize workflow
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
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



  Scenario: Test restriction initialize action
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <restrict-to>
                <conditions>
                    <condition type="phpshell">
                        <arg name="script">return false;</arg>
                    </condition>
                </conditions>
            </restrict-to>
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
    Then Last action was the result of class exception "\OldTown\Workflow\Exception\InvalidRoleException". The massage of exception: "You are restricted from initializing this workflow"



  Scenario: Test register
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <registers>
        <register type="phpshell" variable-name="testCondition">
            <arg name="script">return 'abrakadabra';</arg>
        </register>
    </registers>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <restrict-to>
                <conditions>
                    <condition type="phpshell">
                        <arg name="script">
                            return 'abrakadabra' === $transientVars['testCondition'];
                        </arg>
                    </condition>
                </conditions>
            </restrict-to>
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



  Scenario: Test validator
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <validators>
                <validator type="phpshell">
                    <arg name="script">
                        throw new \InvalidArgumentException('Test validator');
                    </arg>
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
    Then Last action was the result of class exception "\OldTown\Workflow\Exception\InternalWorkflowException". The massage of exception: "Test validator"



#  Scenario: Test postFunction
#    Given : Registrate the workflow with the name "example". With xml:
#  """
#<?xml version="1.0" encoding="UTF-8"?>
#<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
#        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
#<workflow>
#    <initial-actions>
#        <action id="100" name="StartWorkflow">
#            <results>
#                <unconditional-result old-status="Finished" status="Underway" step="10"/>
#            </results>
#        </action>
#    </initial-actions>
#    <steps>
#        <step id="10" name="First">
#            <actions>
#                <action id="200" name="go_to_second_step">
#                    <results>
#                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
#                    </results>
#                </action>
#            </actions>
#        </step>
#        <step id="20" name="Second">
#            <actions>
#                <action id="300" name="test_action_2">
#                    <results>
#                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
#                    </results>
#                </action>
#            </actions>
#        </step>
#    </steps>
#</workflow>
#  """
#    And Create workflow manager
#    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
#         And Call action with id="200" for workflow process with alias "test"

