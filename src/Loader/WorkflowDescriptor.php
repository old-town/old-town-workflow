<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\ArgumentNotNumericException;
use OldTown\Workflow\Exception\InvalidArgumentException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use DOMElement;
use OldTown\Workflow\Exception\RuntimeException;
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
     * @var ActionDescriptor[]|SplObjectStorage
     */
    protected $globalActions;

    /**
     * @var SplObjectStorage|ActionDescriptor[]
     */
    protected $initialActions;

    /**
     * @var JoinDescriptor[]|SplObjectStorage
     */
    protected $joins;

    /**
     * @var SplObjectStorage
     */
    protected $registers;

    /**
     * @var SplitDescriptor[]|SplObjectStorage
     */
    protected $splits = [];

    /**
     * @var StepDescriptor[]|SplObjectStorage
     */
    protected $steps;

    /**
     * @var ActionDescriptor[]
     */
    protected $commonActions = [];

    /**
     * @var ActionDescriptor[]|SplObjectStorage
     */
    protected $commonActionsList = [];

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
        $this->globalActions = new SplObjectStorage();
        $this->steps = new SplObjectStorage();
        $this->joins = new SplObjectStorage();
        $this->splits = new SplObjectStorage();

        parent::__construct($element);

        if (null !== $element) {
            $this->init($element);
        }
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
        $initialActionsElement = XMLUtil::getChildElement($root, 'initial-actions');
        $initialActions = XMLUtil::getChildElement($initialActionsElement, 'action');

        foreach ($initialActions as $initialAction) {
            $actionDescriptor = DescriptorFactory::getFactory()->createActionDescriptor($initialAction);
            $actionDescriptor->setParent($this);
            $this->initialActions->attach($actionDescriptor);
        }


        // handle global-actions - OPTIONAL
        $globalActionsElement = XMLUtil::getChildElement($root, 'global-actions');

        if (null !== $globalActionsElement) {
            $globalActions = XMLUtil::getChildElements($globalActionsElement, 'action');

            foreach ($globalActions as $globalAction) {
                $actionDescriptor = DescriptorFactory::getFactory()->createActionDescriptor($globalAction);
                $actionDescriptor->setParent($this);
                $this->globalActions->attach($actionDescriptor);
            }
        }


        // handle common-actions - OPTIONAL
        //   - Store actions in HashMap for now. When parsing Steps, we'll resolve
        //      any common actions into local references.
        $commonActionsElement = XMLUtil::getChildElement($root, 'common-actions');

        if (null !== $commonActionsElement) {
            $commonActions = XMLUtil::getChildElements($commonActionsElement, 'action');

            foreach ($commonActions as $commonAction) {
                $actionDescriptor = DescriptorFactory::getFactory()->createActionDescriptor($commonAction);
                $actionDescriptor->setParent($this);
                $this->addCommonAction($actionDescriptor);
            }
        }


        // handle timer-functions - OPTIONAL
        $timerFunctionsElement = XMLUtil::getChildElement($root, 'trigger-functions');

        if (null !== $timerFunctionsElement) {
            $timerFunctions = XMLUtil::getChildElements($timerFunctionsElement, 'trigger-function');

            foreach ($timerFunctions as $timerFunction) {
                $function = DescriptorFactory::getFactory()->createFunctionDescriptor($timerFunction);
                $function->setParent($this);
                $id = $function->getId();
                $this->timerFunctions[$id] = $function;
            }
        }

        // handle steps - REQUIRED
        $stepsElement = XMLUtil::getChildElement($root, 'steps');
        $steps = XMLUtil::getChildElements($stepsElement, 'step');

        foreach ($steps as $step) {
            $stepDescriptor = DescriptorFactory::getFactory()->createStepDescriptor($step, $this);
            $this->steps->attach($stepDescriptor);
        }


        // handle splits - OPTIONAL:
        $splitsElement = XMLUtil::getChildElement($root, 'splits');
        if (null !== $splitsElement) {
            $split = XMLUtil::getChildElements($splitsElement, 'split');
            foreach ($split as $s) {
                $splitDescriptor = DescriptorFactory::getFactory()->createSplitDescriptor($s);
                $splitDescriptor->setParent($this);
                $this->splits->attach($splitDescriptor);
            }
        }


        // handle joins - OPTIONAL:
        $joinsElement = XMLUtil::getChildElement($root, 'joins');
        if (null !== $joinsElement) {
            $join = XMLUtil::getChildElements($joinsElement, 'join');
            foreach ($join as $s) {
                $joinDescriptor = DescriptorFactory::getFactory()->createJoinDescriptor($s);
                $joinDescriptor->setParent($this);
                $this->joins->attach($joinDescriptor);
            }
        }
    }

    /**
     * Добавляет новый переход между действиями
     *
     * @param ActionDescriptor $descriptor
     * @return $this
     */
    public function addCommonAction(ActionDescriptor $descriptor)
    {
        $descriptor->setCommon(true);
        $this->addAction($this->commonActions, $descriptor);
        $this->addAction($this->commonActionsList, $descriptor);

        return $this;
    }

    /**
     * @param                  $actionsCollectionOrMap
     * @param ActionDescriptor $descriptor
     *
     * @return $this
     */
    private function addAction($actionsCollectionOrMap, ActionDescriptor $descriptor)
    {
        $descriptorId = $descriptor->getId();
        $action = $this->getAction($descriptorId);
        if (null !== $action) {
            $errMsg = sprintf('action with id "%s" already exists for this step.', $descriptorId);
            throw new InvalidArgumentException($errMsg);
        }

        if ($actionsCollectionOrMap instanceof SplObjectStorage) {
            $actionsCollectionOrMap->attach($descriptor);
            return $this;
        }

        if (is_array($actionsCollectionOrMap)) {
            $actionsCollectionOrMap[$descriptorId] = $descriptor;
            return $this;
        }

        $errMsg = 'Ошибка при добавления перехода workflow';
        throw new RuntimeException($errMsg);
    }

    /**
     * Возвращает шаг по его id
     *
     * @param integer $id
     * @return StepDescriptor|null
     */
    public function getStep($id)
    {
        if (is_numeric($id)) {
            $errMsg = 'Аргумент должен быть числом';
            throw new ArgumentNotNumericException($errMsg);
        }
        $id = (integer)$id;

        foreach ($this->getSteps() as $step) {
            if ($id === $step->getId()) {
                return $step;
            }
        }
        return null;
    }

    /**
     * @param integer $id
     * @return ActionDescriptor|null
     */
    public function getAction($id)
    {
        $id = (integer)$id;

        foreach ($this->getGlobalActions() as $actionDescriptor) {
            if ($id === $actionDescriptor->getId()) {
                return $actionDescriptor;
            }
        }

        foreach ($this->getSteps() as $stepDescriptor) {
            $actionDescriptor = $stepDescriptor->getAction($id);
            if (null !== $actionDescriptor) {
                return $actionDescriptor;
            }
        }

        foreach ($this->getInitialActions() as $actionDescriptor) {
            if ($id === $actionDescriptor->getId()) {
                return $actionDescriptor;
            }
        }

        return null;
    }

    /**
     * @return ActionDescriptor[]|SplObjectStorage
     */
    public function getGlobalActions()
    {
        return $this->globalActions;
    }

    /**
     * @return StepDescriptor[]|SplObjectStorage
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @return ActionDescriptor[]|SplObjectStorage
     */
    public function getInitialActions()
    {
        return $this->initialActions;
    }

    /**
     * @return ActionDescriptor[]
     */
    public function getCommonActions()
    {
        return $this->commonActions;
    }

    /**
     * @param $id
     *
     * @return ActionDescriptor|null
     */
    public function getCommonAction($id)
    {
        $id = (integer)$id;
        if (array_key_exists($id, $this->commonActions)) {
            return $this->commonActions[$id];
        }
        return null;
    }
}
