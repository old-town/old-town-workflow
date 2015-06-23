<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\ArgumentNotNumericException;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use OldTown\Workflow\Exception\InvalidParsingWorkflowException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;
use SplObjectStorage;
use DOMDocument;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class StepDescriptor extends AbstractDescriptor
    implements
        Traits\NameInterface,
        ValidateDescriptorInterface,
        WriteXmlInterface
{
    use Traits\NameTrait, Traits\IdTrait;

    /**
     * @var ActionDescriptor[]|SplObjectStorage
     */
    protected $actions;

    /**
     * Список id дейсвтия являющихся общими для всего workflow
     *
     * @var array
     */
    protected $commonActions = [];

    /**
     * @var FunctionDescriptor[]|SplObjectStorage
     */
    protected $postFunctions;

    /**
     * @var FunctionDescriptor[]|SplObjectStorage
     */
    protected $preFunctions;

    /**
     * @var PermissionDescriptor[]|SplObjectStorage
     */
    protected $permissions;

    /**
     * @var array
     */
    protected $metaAttributes = [];

    /**
     * Определяет есть ли действия для данного шага workflow
     *
     * @var bool
     */
    protected $hasActions = false;


    /**
     * @param DOMElement $element
     * @param AbstractDescriptor $parent
     *
     * @throws InvalidParsingWorkflowException
     */
    public function __construct(DOMElement $element = null, AbstractDescriptor $parent = null)
    {
        $this->preFunctions = new SplObjectStorage();
        $this->postFunctions = new SplObjectStorage();
        $this->actions = new SplObjectStorage();
        $this->permissions = new SplObjectStorage();

        parent::__construct($element);

        if (null !== $parent) {
            $this->setParent($parent);
        }
        if (null !== $element) {
            $this->init($element);
        }
    }

    /**
     * @param DOMElement $step
     *
     * @return void
     * @throws InvalidParsingWorkflowException
     */
    protected function init(DOMElement $step)
    {
        $this->parseId($step);
        $this->parseName($step);


        $metaElements = XmlUtil::getChildElements($step, 'meta');
        foreach ($metaElements as $meta) {
            $value = XmlUtil::getText($meta);
            $name = XmlUtil::getRequiredAttributeValue($meta, 'name');

            $this->metaAttributes[$name] = $value;
        }

        // set up pre-functions -- OPTIONAL
        $pre = XMLUtil::getChildElement($step, 'pre-functions');
        if (null !== $pre) {
            $preFunctions = XMLUtil::getChildElements($pre, 'function');
            foreach ($preFunctions as $preFunction) {
                $functionDescriptor = DescriptorFactory::getFactory()->createFunctionDescriptor($preFunction);
                $functionDescriptor->setParent($this);
                $this->preFunctions->attach($functionDescriptor);
            }
        }

        // set up permissions - OPTIONAL
        $p = XMLUtil::getChildElement($step, 'external-permissions');
        if (null !== $p) {
            $permissions = XMLUtil::getChildElements($pre, 'permission');
            foreach ($permissions as $permission) {
                $permissionDescriptor = DescriptorFactory::getFactory()->createPermissionDescriptor($permission);
                $permissionDescriptor->setParent($this);
                $this->permissions->attach($permissionDescriptor);
            }
        }

        // set up actions - OPTIONAL
        $a = XMLUtil::getChildElement($step, 'actions');
        if (null !== $a) {
            $this->hasActions = true;

            $actions = XMLUtil::getChildElements($a, 'action');
            foreach ($actions as $action) {
                $actionDescriptor = DescriptorFactory::getFactory()->createActionDescriptor($action);
                $actionDescriptor->setParent($this);
                $this->actions->attach($actionDescriptor);
            }

            $commonActions = XMLUtil::getChildElements($a, 'common-action');
            /** @var WorkflowDescriptor $workflowDescriptor */
            $workflowDescriptor = $this->getParent();
            if (!$workflowDescriptor instanceof WorkflowDescriptor) {
                $errMsg = 'Отсутствует Workflow Descriptor';
                throw new InvalidParsingWorkflowException($errMsg);
            }
            foreach ($commonActions as $commonAction) {
                $actionId = XmlUtil::getRequiredAttributeValue($commonAction, 'id');
                $commonActionReference = $workflowDescriptor->getCommonAction($actionId);

                if ($commonActionReference !== null) {
                    $this->actions->attach($commonActionReference);
                }
                $this->commonActions[$actionId] = $actionId;
            }
        }

        // set up post-functions - OPTIONAL

        // set up post-functions - OPTIONAL
        $post = XMLUtil::getChildElement($step, 'post-functions');
        if (null !== $post) {
            $postFunctions = XMLUtil::getChildElements($post, 'function');
            foreach ($postFunctions as $postFunction) {
                $functionDescriptor = DescriptorFactory::getFactory()->createFunctionDescriptor($postFunction);
                $functionDescriptor->setParent($this);
                $this->postFunctions->attach($functionDescriptor);
            }
        }
    }

    /**
     * Возвращает список id дейсвтий являющихся общим для всего workflow
     *
     * @return array
     */
    public function getCommonActions()
    {
        return $this->commonActions;
    }


    /**
     * @return FunctionDescriptor[]|SplObjectStorage
     */
    public function getPostFunctions()
    {
        return $this->postFunctions;
    }

    /**
     * @return FunctionDescriptor[]|SplObjectStorage
     */
    public function getPreFunctions()
    {
        return $this->preFunctions;
    }

    /**
     * @return array
     */
    public function getMetaAttributes()
    {
        return $this->metaAttributes;
    }

    /**
     * @param array $metaAttributes
     *
     * @return $this
     */
    public function setMetaAttributes(array $metaAttributes = [])
    {
        $this->metaAttributes = $metaAttributes;

        return $this;
    }

    /**
     * @return PermissionDescriptor[]|SplObjectStorage
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return ActionDescriptor[]|SplObjectStorage
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param integer $id
     *
     * @return ActionDescriptor|null
     */
    public function getAction($id)
    {
        $id = (integer)$id;
        foreach ($this->actions as $action) {
            if ($id === $action->getId()) {
                return $action;
            }
        }
        return null;
    }

    /**
     * Удаляет действия для данного шага
     *
     * @return $this
     */
    public function removeActions()
    {
        $this->commonActions = [];
        $this->actions = new SplObjectStorage();
        $this->hasActions = false;
        return $this;
    }

    /**
     * @param integer $join
     * @return boolean
     *
     * @throws ArgumentNotNumericException
     */
    public function resultsInJoin($join)
    {
        if (!is_numeric($join)) {
            $errMsg = 'Аргумент должен быть числом';
            throw new ArgumentNotNumericException($errMsg);
        }

        $join = (integer)$join;

        $actions = $this->getActions();

        foreach ($actions as $actionDescriptor) {
            if ($join === $actionDescriptor->getUnconditionalResult()->getJoin()) {
                return true;
            }

            $results = $actionDescriptor->getConditionalResults();
            foreach ($results as $resultDescriptor) {
                if ($join === $resultDescriptor->getJoin()) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Валидация дескриптора
     *
     * @return void
     * @throws InvalidWorkflowDescriptorException
     */
    public function validate()
    {
        $commonActions = $this->getCommonActions();
        $actions = $this->getActions();
        $hasActions = $this->hasActions;

        if ($hasActions && 0 === count($commonActions) && 0 === $actions->count()) {
            $stepName = (string)$this->getName();
            $errMsg = sprintf('Шаг %s должен содержать одни действие или одно общее действие', $stepName);
            throw new InvalidWorkflowDescriptorException($errMsg);
        }

        if (-1 === $this->getId()) {
            $errMsg = 'В качестве id шага нельзя использовать -1, так как это зарезериврованное значение';
            throw new InvalidWorkflowDescriptorException($errMsg);
        }

        $preFunctions = $this->getPreFunctions();
        $postFunctions = $this->getPostFunctions();
        $actions = $this->getActions();
        $permissions = $this->getPermissions();

        ValidationHelper::validate($preFunctions);
        ValidationHelper::validate($postFunctions);
        ValidationHelper::validate($actions);
        ValidationHelper::validate($permissions);

        $workflowDescriptor = $this->getParent();
        if (!$workflowDescriptor instanceof WorkflowDescriptor) {
            $errMsg = sprintf('Родительский элемент для шага должен реализовывать ', WorkflowDescriptor::class);
            throw new InvalidWorkflowDescriptorException($errMsg);
        }
        foreach ($commonActions as $actionId) {
            try {
                $commonActionReference = $workflowDescriptor->getCommonAction($actionId);

                if (null === $commonActionReference) {
                    $stepName = (string)$this->getName();
                    $errMsg = sprintf('Common-action %s указанное для шага %s не существует', $actionId, $stepName);
                    throw new InvalidWorkflowDescriptorException($errMsg);
                }
            } catch (\Exception $e) {
                $actionIdStr = (string)$actionId;
                $errMsg = sprintf('Некорректный id для common-action: id ', $actionIdStr);
                throw  new InvalidWorkflowDescriptorException($errMsg, $e->getCode(), $e);
            }
        }
    }


    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement|null
     * @throws InvalidDescriptorException
     * @throws InvalidWriteWorkflowException
     */
    public function writeXml(DOMDocument $dom = null)
    {
        if (null === $dom) {
            $errMsg = 'Не передан DOMDocument';
            throw new InvalidWriteWorkflowException($errMsg);
        }
        $descriptor = $dom->createElement('step');

        if (!$this->hasId()) {
            $errMsg = 'Отсутствует атрибут id';
            throw new InvalidDescriptorException($errMsg);
        }
        $id = $this->getId();
        $descriptor->setAttribute('id', $id);

        $name = (string)$this->getName();
        $name = trim($name);
        if (strlen($name) > 0) {
            $nameEncode = XmlUtil::encode($name);
            $descriptor->setAttribute('name', $nameEncode);
        }


        $metaAttributes = $this->getMetaAttributes();
        $baseMeta = $dom->createElement('meta');
        foreach ($metaAttributes as $metaAttributeName => $metaAttributeValue) {
            $metaAttributeNameEncode = XmlUtil::encode($metaAttributeName);
            $metaAttributeValueEnEncode = XmlUtil::encode($metaAttributeValue);

            $metaElement = clone $baseMeta;
            $metaElement->setAttribute('name', $metaAttributeNameEncode);
            $metaValueElement = $dom->createTextNode($metaAttributeValueEnEncode);
            $metaElement->appendChild($metaValueElement);

            $descriptor->appendChild($metaElement);
        }


        $preFunctions = $this->getPreFunctions();
        if ($preFunctions->count() > 0) {
            $preFunctionsElement = $dom->createElement('pre-functions');
            foreach ($preFunctions as $function) {
                $functionElement = $function->writeXml($dom);
                $preFunctionsElement->appendChild($functionElement);
            }

            $descriptor->appendChild($preFunctionsElement);
        }


        $permissions = $this->getPermissions();
        if ($permissions->count() > 0) {
            $permissionsElement = $dom->createElement('external-permissions');
            foreach ($permissions as $permission) {
                $permissionElement = $permission->writeXml($dom);
                $permissionsElement->appendChild($permissionElement);
            }

            $descriptor->appendChild($permissionsElement);
        }

        $actions = $this->getActions();
        $commonActions = $this->getCommonActions();

        if ($actions->count() > 0 || count($commonActions) > 0) {
            $actionsElement = $dom->createElement('actions');

            $commonActionElementBase = $dom->createElement('common-action');
            foreach ($commonActions as $commonActionId) {
                $commonActionElement = clone $commonActionElementBase;
                $commonActionElement->setAttribute('id', $commonActionId);

                $actionsElement->appendChild($commonActionElement);
            }

            foreach ($actions as $action) {
                if (!$action->isCommon()) {
                    $actionElement = $action->writeXml($dom);
                    $actionsElement->appendChild($actionElement);
                }
            }

            $descriptor->appendChild($actionsElement);
        }

        $postFunctions = $this->getPostFunctions();
        if ($postFunctions->count() > 0) {
            $postFunctionsElement = $dom->createElement('post-functions');
            foreach ($postFunctions as $function) {
                $functionElement = $function->writeXml($dom);
                $postFunctionsElement->appendChild($functionElement);
            }

            $descriptor->appendChild($postFunctionsElement);
        }


        return $descriptor;
    }
}
