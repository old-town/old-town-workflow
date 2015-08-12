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

  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test exception in children pre-function
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result old-status="Finished" join="2">
        <pre-functions>
            <function type="class" id="80" name="testFunction">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
        </pre-functions>
      </unconditional-result>
    """
    And Get the descriptor using the method of "getPreFunctions"
    And Call a method descriptor "setType". The arguments of the method:
      |type|
      |(null)null|
    And Get the descriptor using the method of "getParent"
    Then I save to descriptor xml. I expect to get an exception message "Ошибка сохранения workflow. Ошибка в pre-function"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test exception in children post-function
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result old-status="Finished" join="2">
        <post-functions>
            <function type="class" id="80" name="testFunction">
              <arg name="class.name">TestClassName</arg>
              <arg name="testArg">testValue</arg>
            </function>
        </post-functions>
      </unconditional-result>
    """
    And Get the descriptor using the method of "getPostFunctions"
    And Call a method descriptor "setType". The arguments of the method:
      |type|
      |(null)null|
    And Get the descriptor using the method of "getParent"
    Then I save to descriptor xml. I expect to get an exception message "Ошибка сохранения workflow. Ошибка в post-function"


  @workflowDescriptor
  Scenario: Create ResultDescriptor. Attempt to write without reference DOMDocument
    Given Create descriptor "ResultDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception "\OldTown\Workflow\Exception\InvalidWriteWorkflowException"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking the preservation of the descriptor in the xml, the incorrect attribute of old-status.
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result old-status="Finished" />
    """
    And Call a method descriptor "setOldStatus". The arguments of the method:
      |oldStatus|
      |(null)null|
    Then I save to descriptor xml. I expect to get an exception message "Некорректное значение для атрибута old-status"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking the preservation of the descriptor in the xml, the incorrect attribute of status.
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result old-status="Finished" />
    """
    Then I save to descriptor xml. I expect to get an exception message "Некорректное значение для атрибута status"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking the preservation of the descriptor in the xml, the incorrect attribute of status.
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result old-status="Finished" status="Queued" />
    """
    Then I save to descriptor xml. I expect to get an exception message "Некорректное значение для атрибута step"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking validation descriptor with incorrect attribute step.
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result id="10" old-status="Finished" status="Queued" />
    """
  Then I validated descriptor. I expect to get an exception message "#10:Если не указано значение атрибутов split или join, необходимо указать id следующего шага"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking validation descriptor with incorrect attribute status.
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result id="10" old-status="Finished" step="1" />
    """
    Then I validated descriptor. I expect to get an exception message "#10:Если не указано значение атрибутов split или join, необходимо указать статус"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking validation descriptor,attribute split.
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result old-status="Finished" split="1" />
    """
    Then Call a method descriptor "validate", I get the value of "(null)null"


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Checking validation descriptor,attribute step and status.
    Given Create descriptor "ResultDescriptor" based on xml:
    """
      <unconditional-result old-status="Finished" step="1" status="Queued"/>
    """
    Then Call a method descriptor "validate", I get the value of "(null)null"