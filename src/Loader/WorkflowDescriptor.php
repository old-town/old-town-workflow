<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\ArgumentNotNumericException;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\InvalidArgumentException;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use DOMElement;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;
use OldTown\Workflow\Exception\RuntimeException;
use SplObjectStorage;
use DOMDocument;
use DOMImplementation;
use \LibXMLError;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class WorkflowDescriptor extends AbstractDescriptor implements WriteXmlInterface
{
    /**
     *
     *
     * @var string
     */
    const DOCUMENT_TYPE_QUALIFIED_NAME = 'workflow';

    /**
     *
     *
     * @var string
     */
    const DOCUMENT_TYPE_PUBLIC_ID = '-//OpenSymphony Group//DTD OSWorkflow 2.8//EN';

    /**
     *
     *
     * @var string
     */
    const DOCUMENT_TYPE_SYSTEM_ID = 'http://www.opensymphony.com/osworkflow/workflow_2_8.dtd';



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
     * @var RegisterDescriptor[]|SplObjectStorage
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
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
     * @throws InternalWorkflowException
     * @throws InvalidDescriptorException
     * @throws InvalidWriteWorkflowException
     * @throws  \OldTown\Workflow\Exception\InvalidDtdSchemaException
     * @return void
     */
    public function validate()
    {
        $registers = $this->getRegisters();
        $triggerFunctions = $this->getTriggerFunctions();
        $globalActions = $this->getGlobalActions();
        $initialActions = $this->getInitialActions();
        $commonActions = $this->getCommonActions();
        $steps = $this->getSteps();
        $splits = $this->getSplits();
        $joins = $this->getJoins();


        ValidationHelper::validate($registers);
        ValidationHelper::validate($triggerFunctions);
        ValidationHelper::validate($globalActions);
        ValidationHelper::validate($initialActions);
        ValidationHelper::validate($commonActions);
        ValidationHelper::validate($steps);
        ValidationHelper::validate($splits);
        ValidationHelper::validate($joins);


        $actions = [];
        $actionsStorage = new SplObjectStorage();

        foreach ($globalActions as $action) {
            $actionId = $action->getId();
            if (array_key_exists($actionId, $actions)) {
                $errMsg = sprintf(
                    'Действие с id %s уже существует ',
                    $actionId
                );
                throw new InvalidWorkflowDescriptorException($errMsg);
            }
            $actions[$actionId] = $actionId;
            $actionsStorage->attach($action);
        }

        foreach ($steps as $step) {
            $j = $step->getActions();

            foreach ($j as $action) {
                if (!$action->isCommon()) {
                    $actionId = $action->getId();
                    if (array_key_exists($actionId, $actions)) {
                        $errMsg = sprintf(
                            'Действие с id %s найденное у шага %s является дубликатом. ',
                            $actionId,
                            $step->getId()
                        );
                        throw new InvalidWorkflowDescriptorException($errMsg);
                    }
                    $actions[$actionId] = $actionId;
                    $actionsStorage->attach($action);
                }
            }
        }


        foreach ($commonActions as $action) {
            if ($actionsStorage->contains($action)) {
                $actionId = $action->getId();
                $errMsg = sprintf(
                    'common-action  с id %s дублирует действие с шага . ',
                    $actionId
                );
                throw new InvalidWorkflowDescriptorException($errMsg);
            }
        }


        $this->validateDtd();
    }

    /**
     * Валидация схемы документа
     *
     * @return void
     * @throws InternalWorkflowException
     * @throws InvalidDescriptorException
     * @throws InvalidWriteWorkflowException
     * @throws  \OldTown\Workflow\Exception\InvalidDtdSchemaException
     * @throws \OldTown\Workflow\Exception\InvalidWorkflowDescriptorException
     */
    private function validateDtd()
    {
        $dom = $this->writeXml();

        $secureDtdEntityResolver = new SecureDtdEntityResolver();
        $dtd = $secureDtdEntityResolver->resolveEntity($dom->doctype);


        $systemId = 'data://text/plain;base64,'.base64_encode($dtd);

        $creator = new DOMImplementation;
        $doctype = $creator->createDocumentType(
            static::DOCUMENT_TYPE_QUALIFIED_NAME,
            static::DOCUMENT_TYPE_PUBLIC_ID,
            $systemId
        );
        $new = $creator->createDocument('', '', $doctype);

        $oldNode = $dom->getElementsByTagName(static::DOCUMENT_TYPE_QUALIFIED_NAME)->item(0);
        $newNode = $new->importNode($oldNode, true);
        $new->appendChild($newNode);

        if (!$new->validate()) {
            /** @var LibXMLError[] $errors */
            $errors = libxml_get_errors();
            $errMsgStack = [];
            foreach ($errors as $error) {
                $errMsgStack[] = $error->message;
            }
            $errMsg = implode(" \n", $errMsgStack);

            throw new InvalidWorkflowDescriptorException($errMsg);
        };
    }

    /**
     * @param DOMElement $root
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
        $initialActions = XMLUtil::getChildElements($initialActionsElement, 'action');

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
     * @return null|ConditionsDescriptor
     */
    public function getGlobalConditions()
    {
        return $this->globalConditions;
    }


    /**
     * Добавляет новый переход между действиями
     *
     * @param ActionDescriptor $descriptor
     * @return $this
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
     * @throws ArgumentNotNumericException
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


    /**
     * @param $id
     *
     * @return ActionDescriptor|null
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function getInitialAction($id)
    {
        if (!is_numeric($id)) {
            $errMsg = 'Аргумент должен быть числом';
            throw new ArgumentNotNumericException($errMsg);
        }

        $initialActions = $this->getInitialActions();
        foreach ($initialActions as $actionDescriptor) {
            if ($id === $actionDescriptor->getId()) {
                return $actionDescriptor;
            }
        }

        return null;
    }

    /**
     * @return JoinDescriptor[]|SplObjectStorage
     */
    public function getJoins()
    {
        return $this->joins;
    }


    /**
     * @param $id
     *
     * @return JoinDescriptor|null
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function getJoin($id)
    {
        if (!is_numeric($id)) {
            $errMsg = 'Аргумент должен быть числом';
            throw new ArgumentNotNumericException($errMsg);
        }

        $joins = $this->getJoins();
        foreach ($joins as $joinDescriptor) {
            if ($id === $joinDescriptor->getId()) {
                return $joinDescriptor;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getMetaAttributes()
    {
        return $this->metaAttributes;
    }

    /**
     * @return RegisterDescriptor[]|SplObjectStorage
     */
    public function getRegisters()
    {
        return $this->registers;
    }

    /**
     * @return SplitDescriptor[]|SplObjectStorage
     */
    public function getSplits()
    {
        return $this->splits;
    }



    /**
     * @param $id
     *
     * @return SplitDescriptor|null
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function getSplit($id)
    {
        if (!is_numeric($id)) {
            $errMsg = 'Аргумент должен быть числом';
            throw new ArgumentNotNumericException($errMsg);
        }

        $splits = $this->getSplits();
        foreach ($splits as $splitDescriptor) {
            if ($id === $splitDescriptor->getId()) {
                return $splitDescriptor;
            }
        }

        return null;
    }

    /**
     * @param integer  $id
     * @param FunctionDescriptor $descriptor
     * @return $this
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function setTriggerFunction($id, FunctionDescriptor $descriptor)
    {
        if (!is_numeric($id)) {
            $errMsg = 'Аргумент должен быть числом';
            throw new ArgumentNotNumericException($errMsg);
        }
        $id = (integer)$id;
        $this->timerFunctions[$id] = $descriptor;

        return $this;
    }

    /**
     * @param integer  $id
     * @return FunctionDescriptor
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function getTriggerFunction($id)
    {
        if (!is_numeric($id)) {
            $errMsg = 'Аргумент должен быть числом';
            throw new ArgumentNotNumericException($errMsg);
        }
        $id = (integer)$id;

        if (!array_key_exists($id, $this->timerFunctions)) {
            $errMsg = sprintf('Не найдена trigger-function с id %s', $id);
            throw new ArgumentNotNumericException($errMsg);
        }

        $this->timerFunctions[$id];

        return $this->timerFunctions[$id];
    }

    /**
     * @return FunctionDescriptor[]
     */
    public function getTriggerFunctions()
    {
        return $this->timerFunctions;
    }

    /**
     * @param ActionDescriptor $descriptor
     * @return $this
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function addGlobalAction(ActionDescriptor $descriptor)
    {
        $this->addAction($this->globalActions, $descriptor);
        return $this;
    }

    /**
     * @param ActionDescriptor $descriptor
     * @return $this
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function addInitialAction(ActionDescriptor $descriptor)
    {
        $this->addAction($this->initialActions, $descriptor);
        return $this;
    }


    /**
     * @param JoinDescriptor $descriptor
     * @return $this
     * @throws InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function addJoin(JoinDescriptor $descriptor)
    {
        $id = $descriptor->getId();
        if (null !== $this->getJoin($id)) {
            $errMsg = sprintf('Объеденение с id %s уже существует', $id);
            throw new InvalidArgumentException($errMsg);
        }

        $this->getJoins()->attach($descriptor);
        return $this;
    }


    /**
     * @param SplitDescriptor $descriptor
     * @return $this
     * @throws InvalidArgumentException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function addSplit(SplitDescriptor $descriptor)
    {
        $id = $descriptor->getId();
        if (null !== $this->getSplit($id)) {
            $errMsg = sprintf('Ветвление с id %s уже существует', $id);
            throw new InvalidArgumentException($errMsg);
        }

        $this->getSplits()->attach($descriptor);
        return $this;
    }

    /**
     * @param StepDescriptor $descriptor
     * @return $this
     *
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     * @throws ArgumentNotNumericException
     */
    public function addStep(StepDescriptor $descriptor)
    {
        $id = $descriptor->getId();
        if (null !== $this->getStep($id)) {
            $errMsg = sprintf('Шаг с id %s уже существует', $id);
            throw new InvalidArgumentException($errMsg);
        }

        $this->getSteps()->attach($descriptor);
        return $this;
    }

    /**
     * @param ActionDescriptor $actionToRemove
     * @return boolean
     */
    public function removeAction(ActionDescriptor $actionToRemove)
    {
        $actionToRemoveId = $actionToRemove->getId();
        $globalActions = $this->getGlobalActions();
        foreach ($globalActions as $actionDescriptor) {
            if ($actionToRemoveId === $actionDescriptor->getId()) {
                $globalActions->detach($actionDescriptor);

                return true;
            }
        }

        $steps = $this->getSteps();
        foreach ($steps as $stepDescriptor) {
            $actionDescriptor = $stepDescriptor->getAction($actionToRemoveId);

            if (null !== $actionDescriptor) {
                $stepDescriptor->getActions()->detach($actionDescriptor);

                return true;
            }
        }

        return false;
    }


    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMDocument
     * @throws InternalWorkflowException
     * @throws InvalidDescriptorException
     * @throws InvalidWriteWorkflowException
     *
     */
    public function writeXml(DOMDocument $dom = null)
    {
        if (null === $dom) {
            $imp = new DOMImplementation();
            $dtd  = $imp->createDocumentType(
                static::DOCUMENT_TYPE_QUALIFIED_NAME,
                static::DOCUMENT_TYPE_PUBLIC_ID,
                static::DOCUMENT_TYPE_SYSTEM_ID
            );
            $dom = $imp->createDocument('', '', $dtd);
            $dom->encoding = 'UTF-8';
            $dom->xmlVersion = '1.0';
            $dom->formatOutput = true;
        }

        $descriptor = $dom->createElement('workflow');

        $metaAttributes = $this->getMetaAttributes();
        $metaElementBase = $dom->createElement('meta');
        foreach ($metaAttributes as $metaAttributeName => $metaAttributeValue) {
            $metaAttributeNameEncode = XmlUtil::encode($metaAttributeName);
            $metaAttributeValueEnEncode = XmlUtil::encode($metaAttributeValue);

            $metaElement = clone $metaElementBase;
            $metaElement->setAttribute('name', $metaAttributeNameEncode);
            $metaValueElement = $dom->createTextNode($metaAttributeValueEnEncode);
            $metaElement->appendChild($metaValueElement);

            $descriptor->appendChild($metaElement);
        }

        $registers = $this->getRegisters();
        if ($registers->count() > 0) {
            $registersElement = $dom->createElement('registers');
            foreach ($registers as $register) {
                $registerElement = $register->writeXml($dom);
                $registersElement->appendChild($registerElement);
            }

            $descriptor->appendChild($registersElement);
        }


        $timerFunctions = $this->getTriggerFunctions();
        if (count($timerFunctions) > 0) {
            $timerFunctionsElement = $dom->createElement('trigger-functions');
            $timerFunctionElementBase = $dom->createElement('trigger-function');
            foreach ($timerFunctions as $timerFunctionId => $timerFunction) {
                $timerFunctionElement = clone $timerFunctionElementBase;
                $timerFunctionElement->setAttribute('id', $timerFunctionId);
                $functionElement = $timerFunction->writeXml($dom);
                $timerFunctionElement->appendChild($functionElement);

                $timerFunctionsElement->appendChild($timerFunctionElement);
            }
            $descriptor->appendChild($timerFunctionsElement);
        }


        $globalConditions = $this->getGlobalConditions();
        if (null !== $globalConditions) {
            $globalConditionsElement = $dom->createElement('global-conditions');
            $globalConditionElement = $globalConditions->writeXml($dom);
            $globalConditionsElement->appendChild($globalConditionElement);
            $descriptor->appendChild($globalConditionsElement);
        }


        $initialActionsElement = $dom->createElement('initial-actions');
        $initialActions = $this->getInitialActions();
        foreach ($initialActions as $initialAction) {
            $initialActionElement = $initialAction->writeXml($dom);
            $initialActionsElement->appendChild($initialActionElement);
        }
        $descriptor->appendChild($initialActionsElement);


        $globalActions = $this->getGlobalActions();
        if ($globalActions->count() > 0) {
            $globalActionsElement = $dom->createElement('global-actions');
            foreach ($globalActions as $globalAction) {
                try {
                    $globalActionElement = $globalAction->writeXml($dom);
                } catch (\Exception $e) {
                    $errMsg  = 'Ошибка генерации workflow';
                    throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
                }

                $globalActionsElement->appendChild($globalActionElement);
            }

            $descriptor->appendChild($globalActionsElement);
        }

        $commonActions = $this->getCommonActions();
        if (count($commonActions) > 0) {
            $commonActionsElement = $dom->createElement('common-actions');
            foreach ($commonActions as $commonAction) {
                try {
                    $commonActionElement = $commonAction->writeXml($dom);
                } catch (\Exception $e) {
                    $errMsg  = 'Ошибка генерации workflow';
                    throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
                }
                $commonActionsElement->appendChild($commonActionElement);
            }

            $descriptor->appendChild($commonActionsElement);
        }


        $stepsElement = $dom->createElement('steps');
        $steps = $this->getSteps();
        foreach ($steps as $step) {
            try {
                $stepElement = $step->writeXml($dom);
            } catch (\Exception $e) {
                $errMsg  = 'Ошибка генерации workflow';
                throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
            }
            $stepsElement->appendChild($stepElement);
        }

        $descriptor->appendChild($stepsElement);

        $splits = $this->getSplits();
        if ($splits->count() > 0) {
            $splitsElement = $dom->createElement('splits');
            foreach ($splits as $split) {
                $splitElement = $split->writeXml($dom);
                $splitsElement->appendChild($splitElement);
            }

            $descriptor->appendChild($splitsElement);
        }

        $joins = $this->getJoins();
        if ($joins->count() > 0) {
            $joinsElement = $dom->createElement('joins');
            foreach ($joins as $join) {
                $joinElement = $join->writeXml($dom);
                $joinsElement->appendChild($joinElement);
            }

            $descriptor->appendChild($joinsElement);
        }

        $dom->appendChild($descriptor);
        return $dom;
    }
}
