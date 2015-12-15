Feature:Workflow action


  Scenario: Test action preFunction
    Given : Registrate the workflow with the name "example". With xml:
  """
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE workflow PUBLIC "-//OpenSymphony Group//DTD OSWorkflow 2.6//EN"
        "http://www.opensymphony.com/osworkflow/workflow_2_8.dtd">
<workflow>
    <initial-actions>
        <action id="100" name="StartWorkflow">
            <results>
                <unconditional-result old-status="Finished" status="Underway" step="10"/>
            </results>
        </action>
    </initial-actions>
    <steps>
        <step id="10" name="First">
            <actions>
                <action id="200" name="go_to_second_step">
                  <pre-functions>
                      <function type="phpshell">
                          <arg name="script">
                              $transientVars['actionPreFunction'] = 'real work!';
                          </arg>
                      </function>
                  </pre-functions>
                    <results>
                        <unconditional-result old-status="Finished" status="Underway"  step="20">
                            <validators>
                                <validator type="phpshell">
                                    <arg name="script">
                                        if ('real work!' !== $transientVars['actionPreFunction']) {
                                            throw new \InvalidArgumentException('Step Post Function Not Work');
                                        }
                                    </arg>
                                </validator>
                            </validators>
                        </unconditional-result>
                    </results>
                </action>
            </actions>
        </step>
        <step id="20" name="Second">
            <actions>
                <action id="300" name="test_action_2">
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
    And Call action with id="200" for workflow process with alias "test"
    Then Process of workflow with the alias "test" has the below steps:
      |stepId|
      |20    |
    And Exceptions are missing


