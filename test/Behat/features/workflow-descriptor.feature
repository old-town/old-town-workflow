Feature:Workflow Descriptor
  test


Scenario: Create empty workflow
  Given Create "RegisterDescriptor" based on xml:
    """
        <register type="class" variable-name="log">
            <arg name="class.name">\OldTown\Workflow\Util\LogRegister</arg>
            <arg name="addInstanceId">true</arg>
        </register>
    """
