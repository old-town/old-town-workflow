Feature:Workflow Descriptor
  test

@workflowDescriptor
Scenario: Create RegisterDescriptor the type of "class"
  Given Create "RegisterDescriptor" based on xml:
    """
        <register type="class" variable-name="log">
            <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
            <arg name="addInstanceId">true</arg>
        </register>
    """
  Then Call a method descriptor "getVariableName", I get the value of "log"
    And Call a method descriptor "getType", I get the value of "class"

