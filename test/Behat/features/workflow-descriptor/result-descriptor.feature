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


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test split attribute. Validate save in xml descriptor
    Given Create descriptor "ResultDescriptor" based on xml:
    """
        <unconditional-result old-status="Finished" split="2"/>
    """
    Then I save to descriptor xml. Compare with xml:
    """
        <unconditional-result old-status="Finished" split="2"/>
    """

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test join attribute. Validate save in xml descriptor
    Given Create descriptor "ResultDescriptor" based on xml:
    """
        <unconditional-result old-status="Finished" join="1"/>
    """
    Then I save to descriptor xml. Compare with xml:
    """
        <unconditional-result old-status="Finished" join="1"/>
    """



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test set display-name attribute. A parent descriptor specified attribute "name"
    Given Create descriptor "ActionDescriptor" based on xml:
    """
        <action id="12" name="test-name">
            <results>
                <unconditional-result old-status="Finished" status="Queued"  step="1"/>
            </results>
        </action>
    """
      And Get the descriptor using the method of "getUnconditionalResult"
    When Call a method descriptor "setDisplayName". The arguments of the method:
      |displayName|
      |test-name|
    Then Call a method descriptor "getDisplayName", I get the value of "(null)null"

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test set display-name attribute. A parent tag is not set attribute "name"
    Given Create descriptor "ActionDescriptor" based on xml:
    """
        <action id="12">
            <results>
                <unconditional-result old-status="Finished" status="Queued"  step="1"/>
            </results>
        </action>
    """
    And Get the descriptor using the method of "getUnconditionalResult"
    When Call a method descriptor "setDisplayName". The arguments of the method:
      |displayName|
      |test-name|
    Then Call a method descriptor "getDisplayName", I get the value of "test-name"