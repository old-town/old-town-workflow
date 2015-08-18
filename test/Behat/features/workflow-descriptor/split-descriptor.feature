Feature: Split Descriptor



  @workflowDescriptor
  Scenario: Create a descriptor from xml. Validate save in xml descriptor
    Given Create descriptor "SplitDescriptor" based on xml:
    """
      <split id="1">
          <unconditional-result old-status="Finished" status="Underway" owner="test" step="2"/>
      </split>
    """
    Then I save to descriptor xml. Compare with xml:
    """
      <split id="1">
          <unconditional-result old-status="Finished" status="Underway" owner="test" step="2"/>
      </split>
    """


  @workflowDescriptor
  Scenario: Create SplitDescriptor. Attempt to write without reference DOMDocument
    Given Create descriptor "SplitDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Не передан DOMDocument"


  @workflowDescriptor
  Scenario: Create SplitDescriptor. Do not set the id attribute.
    Given Create descriptor "SplitDescriptor"
    Then Call a method descriptor "writeXml". I expect to get an exception message "Отсутствует атрибут id". The arguments of the method:
      |dom|
      |(DOMDocument)domDocument|


  @workflowDescriptor
  Scenario: Create a descriptor from xml. Test validate method. Valid descriptor.
    Given Create descriptor "SplitDescriptor" based on xml:
    """
      <split id="1">
          <unconditional-result old-status="Finished" status="Underway" owner="test" step="2"/>
      </split>
    """
    When Call a method descriptor "validate", I get the value of "(null)null"

