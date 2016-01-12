Feature:Workflow conditions


  Scenario: Test conditions. Conditions type = OR. All conditions return false
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="20"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="20" name="Second">
            <actions>
                <action id="300" name="test_action_2">
                    <restrict-to>
                        <conditions type="OR">
                            <condition type="phpshell">
                                <arg name="script">
                                    return false;
                                </arg>
                            </condition>
                            <condition type="phpshell">
                                <arg name="script">
                                    return false;
                                </arg>
                            </condition>
                            <condition type="phpshell">
                                <arg name="script">
                                    return false;
                                </arg>
                            </condition>
                        </conditions>
                    </restrict-to>
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
    And Create workflow manager
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
    And Call action with id="300" for workflow process with alias "test"
    Then Last action was the result of class exception "\OldTown\Workflow\Exception\InvalidActionException". The massage of exception: "Action 300 is invalid"


    Scenario: Test conditions. Empty conditions
        Given : Registrate the workflow with the name "example". With xml:
    """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="20"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="20" name="Second">
            <actions>
                <action id="300" name="test_action_2">
                    <restrict-to>
                        <conditions>

                        </conditions>
                    </restrict-to>
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
        And Create workflow manager
        When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
        And Call action with id="300" for workflow process with alias "test"
        Then Process of workflow with the alias "test" has the below steps:
            |stepId|
            |20     |
        And Exceptions are missing



  Scenario: Test conditions. Test "AND"
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="20"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="20" name="Second">
            <actions>
                <action id="300" name="test_action_2">
                    <restrict-to>
                        <conditions type="AND">
                            <condition type="phpshell">
                                <arg name="script">
                                    return true;
                                </arg>
                            </condition>
                            <condition type="phpshell">
                                <arg name="script">
                                    return false;
                                </arg>
                            </condition>
                            <condition type="phpshell">
                                <arg name="script">
                                    return true;
                                </arg>
                            </condition>
                        </conditions>
                    </restrict-to>
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
    And Create workflow manager
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
    And Call action with id="300" for workflow process with alias "test"
    Then Last action was the result of class exception "\OldTown\Workflow\Exception\InvalidActionException". The massage of exception: "Action 300 is invalid"



  Scenario: Test conditions. Test "AND". All conditions return "true"
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="20"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="20" name="Second">
            <actions>
                <action id="300" name="test_action_2">
                    <restrict-to>
                        <conditions type="AND">
                            <condition type="phpshell">
                                <arg name="script">
                                    return true;
                                </arg>
                            </condition>
                            <condition type="phpshell">
                                <arg name="script">
                                    return true;
                                </arg>
                            </condition>
                            <condition type="phpshell">
                                <arg name="script">
                                    return true;
                                </arg>
                            </condition>
                        </conditions>
                    </restrict-to>
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
    And Create workflow manager
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
    And Call action with id="300" for workflow process with alias "test"
    Then Process of workflow with the alias "test" has the below steps:
      |stepId|
      |20     |
    And Exceptions are missing



  Scenario: Test initial action conditions result.
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="Start_Workflow">
            <results>
                <result old-status="Finished" status="Underway" step="30">
                    <conditions type="AND">
                        <condition type="phpshell">
                            <arg name="script">
                                return true;
                            </arg>
                        </condition>
                        <condition type="phpshell">
                            <arg name="script">
                                return true;
                            </arg>
                        </condition>
                        <condition type="phpshell">
                            <arg name="script">
                                return true;
                            </arg>
                        </condition>
                    </conditions>
                </result>
                <unconditional-result old-status="Finished" status="Underway" step="20"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="20" name="step_20">
            <actions>
                <action id="300" name="test_action_2">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
        <step id="30" name="step_30">
            <actions>
                <action id="400" name="test_action_3">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="30"/>
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
      |30     |
    And Exceptions are missing



  Scenario: Test condition argumnet black magic.
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="20"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="20" name="Second">
            <actions>
                <action id="300" name="test_action_2">
                    <restrict-to>
                        <conditions type="AND">
                            <condition type="phpshell">
                                <arg name="script">
                                <![CDATA[
                                    return array_key_exists('stepId', $args) && true;
                                ]]>
                                </arg>
                                <arg name="stepId">-1</arg>
                            </condition>
                        </conditions>
                    </restrict-to>
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
    And Create workflow manager
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
    And Call action with id="300" for workflow process with alias "test"
    Then Process of workflow with the alias "test" has the below steps:
      |stepId|
      |20     |
    And Exceptions are missing


  Scenario: Test invalid condition provider
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="20"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="20" name="Second">
            <actions>
                <action id="300" name="test_action_2">
                    <results>
                        <result old-status="Finished" status="Underway"  step="20">
                            <conditions type="AND">
                                <condition type="phpshell_invalid">
                                    <arg name="script">
                                        return true;
                                    </arg>
                                </condition>
                            </conditions>
                        </result>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
    </steps>
</workflow>
  """
    And Create workflow manager
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "100"
    And Call action with id="300" for workflow process with alias "test"
    Then Last action was the result of class exception "\OldTown\Workflow\Exception\WorkflowException". The massage of exception: "Нет типа(phpshell_invalid) или аргумента class.name"

