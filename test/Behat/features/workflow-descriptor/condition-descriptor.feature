Feature:Condition Descriptor

@workflowDescriptor
Scenario: Create ConditionDescriptor the type of "class"
  Given Create descriptor "ConditionDescriptor" based on xml:
    """
      <condition type="class">
          <arg name="class.name">TestConditionDescriptorClassName</arg>
          <arg name="testArg">testValue</arg>
      </condition>
    """
  Then Call a method descriptor "getType", I get the value of "class"
    And Call a method descriptor "getArg", I get the value of "TestConditionDescriptorClassName". The arguments of the method:
      |name|
      |class.name|
    And Call a method descriptor "getArg", I get the value of "testValue". The arguments of the method:
      |name|
      |testArg|
