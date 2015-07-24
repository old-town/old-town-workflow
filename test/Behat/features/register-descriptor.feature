Feature:Register Descriptor

@workflowDescriptor
Scenario: Create RegisterDescriptor the type of "class"
  Given Create "RegisterDescriptor" based on xml:
    """
        <register type="class" variable-name="log" id="1">
            <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
            <arg name="addInstanceId">true</arg>
        </register>
    """
  Then Call a method descriptor "getVariableName", I get the value of "log"
    And Call a method descriptor "getType", I get the value of "class"
    And Call a method descriptor "getId", I get the value of "1"
    And Call a method descriptor "getArg", I get the value of "\OldTown\Workflow\Util\LogRegister". The arguments of the method:
      |name|
      |class.name|
    And Call a method descriptor "getArg", I get the value of "true". The arguments of the method:
      |name|
      |addInstanceId|