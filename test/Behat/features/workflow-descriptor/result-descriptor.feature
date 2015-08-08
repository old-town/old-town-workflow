Feature:Result Descriptor


  @workflowDescriptor
  Scenario: Create a descriptor from xml.
    Validate save in xml descriptor
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result id="10" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
        <validators>
            <validator type="class" name="validator-name1" id="50">
              <arg name="class.name">TestValidatorClass</arg>
              <arg name="addInstanceId">true</arg>
            </validator>
            <validator type="phpshell" name="validator-name2" id="60">
                <arg name="script"><![CDATA[echo 'test';]]></arg>
            </validator>
        </validators>
        <pre-functions>
            <function type="class" id="80" name="testFunction">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="90" name="testFunction2">
              <arg name="script">echo 'test';</arg>
            </function>
        </pre-functions>
        <post-functions>
            <function type="class" id="100" name="testFunction3">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="120" name="testFunction4">
              <arg name="script">echo 'test';</arg>
            </function>
        </post-functions>
      </unconditional-result>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <unconditional-result id="10" old-status="Finished" status="Queued" step="2" due-date="2001-03-10 17:16:18" owner="my" display-name="test-display">
        <validators>
            <validator type="class" name="validator-name1" id="50">
              <arg name="class.name">TestValidatorClass</arg>
              <arg name="addInstanceId">true</arg>
            </validator>
            <validator type="phpshell" name="validator-name2" id="60">
                <arg name="script"><![CDATA[echo 'test';]]></arg>
            </validator>
        </validators>
        <pre-functions>
            <function type="class" id="80" name="testFunction">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="90" name="testFunction2">
              <arg name="script">echo 'test';</arg>
            </function>
        </pre-functions>
        <post-functions>
            <function type="class" id="100" name="testFunction3">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
            <function type="phpshell" id="120" name="testFunction4">
              <arg name="script">echo 'test';</arg>
            </function>
        </post-functions>
      </unconditional-result>
    """
