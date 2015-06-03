<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;

use DOMElement;
use SplObjectStorage;


/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class WorkflowDescriptor extends AbstractDescriptor
{
    /**
     * @var ConditionsDescriptor|null
     */
    protected $globalConditions;

    /**
     * @var array
     */
    protected $globalActions = [];

    /**
     * @var SplObjectStorage|ActionDescriptor[]
     */
    protected $initialActions;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var SplObjectStorage
     */
    protected $registers;

    /**
     * @var array
     */
    protected $splits = [];

    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @var array
     */
    protected $commonActions = [];

    /**
     * @var array
     */
    protected $metaAttributes = [];

    /**
     * @var array
     */
    protected $timerFunctions = [];

    /**
     * Имя workflow
     *
     * @var string|null
     */
    protected $workflowName;

    /**
     * @param DOMElement $element
     */
    public function __construct(DOMElement $element = null)
    {
        $this->registers = new SplObjectStorage();
        $this->initialActions = new SplObjectStorage();

        if (null !== $element) {
            $this->init($element);
        }

        parent::__construct($element);
    }

    /**
     * Возвращает имя workflow
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->workflowName;
    }

    /**
     * Устанавливает имя workflow
     *
     * @param string $workflowName
     *
     * @return $this
     */
    public function setName($workflowName)
    {
        $this->workflowName = (string)$workflowName;

        return $this;
    }

    /**
     * Валидация workflow
     *
     * @throws InvalidWorkflowDescriptorException
     * @return void
     */
    public function validate()
    {

    }

    /**
     * @param DOMElement $root
     */
    protected function init(DOMElement $root)
    {
        $metaElements = XmlUtil::getChildElements($root, 'meta');
        foreach ($metaElements as $meta) {

            $value = XmlUtil::getText($meta);
            $name = XmlUtil::getRequiredAttributeValue($meta, 'name');

            $this->metaAttributes[$name] = $value;
        }

        // handle registers - OPTIONAL
        $r = XmlUtil::getChildElement($root, 'registers');
        if (null !== $r) {
            $registers = XMLUtil::getChildElements($r, 'register');

            foreach ($registers as $register) {
                $registerDescriptor = DescriptorFactory::getFactory()->createRegisterDescriptor($register);
                $registerDescriptor->setParent($this);
                $this->registers->attach($registerDescriptor);
            }
        }

        // handle global-conditions - OPTIONAL
        $globalConditionsElement = XMLUtil::getChildElement($root, 'global-conditions');
        if ($globalConditionsElement !== null) {
            $globalConditions = XMLUtil::getChildElement($globalConditionsElement, 'conditions');

            $conditionsDescriptor = DescriptorFactory::getFactory()->createConditionsDescriptor($globalConditions);
            $conditionsDescriptor->setParent($this);
            $this->globalConditions = $conditionsDescriptor;
        }

        // handle initial-steps - REQUIRED
        $intialActionsElement = XMLUtil::getChildElements($root, 'initial-actions');
        $initialActions = XMLUtil::getChildElement($intialActionsElement, 'action');

        foreach ($initialActions as $initialAction) {
            $actionDescriptor = DescriptorFactory::getFactory()->createActionDescriptor($initialAction);
            $actionDescriptor->setParent($this);
            $this->initialActions->attach($actionDescriptor);
        }
    }
}
