Feature:Conditions Result

  Scenario: Test conditions result
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="50" name="StartWorkflow">
            <results>
                <result old-status="Finished" status="Underway" step="10">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return false;
                            </arg>
                        </condition>
                    </conditions>
                </result>
                <result old-status="Finished" status="Underway" step="20">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return false;
                            </arg>
                        </condition>
                    </conditions>
                </result>
                <result old-status="Finished" status="Underway" step="30">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return true;
                            </arg>
                        </condition>
                    </conditions>
                </result>
                <unconditional-result old-status="Finished" status="Underway" step="10"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="10" name="step_10">
            <actions>
                <action id="200" name="go_to_step_10">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="10" />
                    </results>
                </action>
            </actions>
        </step>
        <step id="20" name="step_20">
            <actions>
                <action id="300" name="go_to_step_20">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
        <step id="30" name="step_30">
            <actions>
                <action id="400" name="go_to_step_30">
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
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "50"
    Then Process of workflow with the alias "test" has the below steps:
      |stepId|
      |30    |
    And Exceptions are missing



  Scenario: Test conditions result. Test validator.
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="50" name="StartWorkflow">
            <results>
                <result old-status="Finished" status="Underway" step="10">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return false;
                            </arg>
                        </condition>
                    </conditions>
                </result>
                <result old-status="Finished" status="Underway" step="20">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return false;
                            </arg>
                        </condition>
                    </conditions>
                </result>
                <result old-status="Finished" status="Underway" step="30">
                    <validators>
                        <validator type="phpshell">
                            <arg name="script">
                                throw new \InvalidArgumentException('Test validator condition result');
                            </arg>
                        </validator>
                    </validators>
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return true;
                            </arg>
                        </condition>
                    </conditions>
                </result>
                <unconditional-result old-status="Finished" status="Underway" step="10"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="10" name="step_10">
            <actions>
                <action id="200" name="go_to_step_10">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="10" />
                    </results>
                </action>
            </actions>
        </step>
        <step id="20" name="step_20">
            <actions>
                <action id="300" name="go_to_step_20">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
        <step id="30" name="step_30">
            <actions>
                <action id="400" name="go_to_step_30">
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
    When Progress workflow with alias "test". Workflow name: "example". Initial action id: "50"
    Then Last action was the result of class exception "\OldTown\Workflow\Exception\InternalWorkflowException". The massage of exception: "Test validator condition result"




    Scenario: Test conditions result. Test condition pre and post function.
        Given : Registrate the workflow with the name "example". With xml:
    """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="50" name="StartWorkflow">
            <results>
                <result old-status="Finished" status="Underway" step="10">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return false;
                            </arg>
                        </condition>
                    </conditions>
                    <pre-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['preFunctions'] = 'preFunction_1';
                            </arg>
                        </function>
                    </pre-functions>
                    <post-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['postFunctions'] = 'postFunction_1';
                            </arg>
                        </function>
                    </post-functions>
                </result>
                <result old-status="Finished" status="Underway" step="20">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return false;
                            </arg>
                        </condition>
                    </conditions>
                    <pre-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['preFunctions'] = 'preFunction_2';
                            </arg>
                        </function>
                    </pre-functions>
                    <post-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['postFunctions'] = 'postFunction_2';
                            </arg>
                        </function>
                    </post-functions>
                </result>
                <result old-status="Finished" status="Underway" step="30">
                    <conditions>
                        <condition type="phpshell">
                            <arg name="script">
                                return true;
                            </arg>
                        </condition>
                    </conditions>
                    <pre-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['preFunctions'] = 'preFunction_3';
                            </arg>
                        </function>
                    </pre-functions>
                    <post-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['postFunctions'] = 'postFunction_3';
                            </arg>
                        </function>
                    </post-functions>
                </result>
                <unconditional-result old-status="Finished" status="Underway" step="10">
                    <post-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['postFunctions'] = 'postFunction_4';
                            </arg>
                        </function>
                    </post-functions>
                    <pre-functions>
                        <function type="phpshell">
                            <arg name="script">
                                $transientVars['preFunctions'] = 'preFunction_4';
                            </arg>
                        </function>
                    </pre-functions>
                </unconditional-result>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="10" name="step_10">
            <actions>
                <action id="200" name="go_to_step_10">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="10" />
                    </results>
                </action>
            </actions>
        </step>
        <step id="20" name="step_20">
            <actions>
                <action id="300" name="go_to_step_20">
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20"/>
                    </results>
                </action>
            </actions>
        </step>
        <step id="30" name="step_30">
            <actions>
                <action id="400" name="go_to_step_30">
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
        When Progress workflow with alias "test". Workflow name: "example". Initial action id: "50"
        Then Process of workflow with the alias "test" has the below steps:
            |stepId|
            |30    |
            And Exceptions are missing
            And There is the valuable "preFunctions" with value "preFunction_3" in Transient Vars
            And There is the valuable "postFunctions" with value "postFunction_3" in Transient Vars
