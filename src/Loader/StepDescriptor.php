<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\InvalidParsingWorkflowException;
use OldTown\Workflow\Loader\Traits;
use SplObjectStorage;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class StepDescriptor extends AbstractDescriptor implements Traits\NameInterface
{
    use Traits\NameTrait, Traits\IdTrait;

    /**
     * @var ActionDescriptor[]|SplObjectStorage
     */
    protected $actions;

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
     * @param DOMElement         $element
     * @param AbstractDescriptor $parent
     */
    public function __construct(DOMElement $element = null, AbstractDescriptor $parent = null)
    {
        $this->preFunctions = new SplObjectStorage();
        $this->postFunctions = new SplObjectStorage();
        $this->actions  = new SplObjectStorage();

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
}
