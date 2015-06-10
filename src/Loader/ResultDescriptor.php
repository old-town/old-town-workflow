<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use SplObjectStorage;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ResultDescriptor extends AbstractDescriptor
{
    use Traits\IdTrait;

    /**
     * Предыдущий статус
     *
     * @var string
     */
    protected $oldStatus;

    /**
     * Статус
     *
     * @var string
     */
    protected $status;

    /**
     * Срок
     *
     * @var string
     */
    protected $dueDate;

    /**
     * @var integer|null
     */
    protected $join;

    /**
     * @var integer|null
     */
    protected $split;

    /**
     * @var integer
     */
    protected $step = 0;

    /**
     * @var bool
     */
    protected $hasStep = false;

    /**
     * @var string
     */
    protected $owner;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var ValidatorDescriptor[]|SplObjectStorage
     */
    protected $validators;

    /**
     * @var FunctionDescriptor[]|SplObjectStorage
     */
    protected $preFunctions;

    /**
     * @var FunctionDescriptor[]|SplObjectStorage
     */
    protected $postFunctions;

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        $this->validators = new SplObjectStorage();
        $this->preFunctions = new SplObjectStorage();
        $this->postFunctions = new SplObjectStorage();

        parent::__construct($element);

        if (null !== $element) {
            $this->init($element);
        }
    }

    /**
     * @param DOMElement $result
     *
     * @return void
     */
    protected function init(DOMElement $result)
    {
        $this->oldStatus = XmlUtil::getRequiredAttributeValue($result, 'old-status');
        if ($result->hasAttribute('status')) {
            $this->status = XmlUtil::getRequiredAttributeValue($result, 'status');
        }

        $this->parseId($result, false);

        if ($result->hasAttribute('due-date')) {
            $this->dueDate = XmlUtil::getRequiredAttributeValue($result, 'due-date');
        }

        if ($result->hasAttribute('split')) {
            $split = XmlUtil::getRequiredAttributeValue($result, 'split');
            $this->setSplit($split);
        }


        if ($result->hasAttribute('join')) {
            $join = XmlUtil::getRequiredAttributeValue($result, 'join');
            $this->setJoin($join);
        }

        if ($result->hasAttribute('step')) {
            $step = XmlUtil::getRequiredAttributeValue($result, 'step');
            $this->setStep($step);
        }

        if ($result->hasAttribute('owner')) {
            $owner = XmlUtil::getRequiredAttributeValue($result, 'owner');
            $this->setOwner($owner);
        }

        if ($result->hasAttribute('display-name')) {
            $displayName = XmlUtil::getRequiredAttributeValue($result, 'display-name');
            $this->setOwner($displayName);
        }

        // set up validators -- OPTIONAL
        $v = XMLUtil::getChildElement($result, 'validators');
        if (null !== $v) {
            $validators = XMLUtil::getChildElements($v, 'validator');
            foreach ($validators as $validator) {
                $validatorDescriptor = DescriptorFactory::getFactory()->createValidatorDescriptor($validator);
                $validatorDescriptor->setParent($this);
                $this->validators->attach($validatorDescriptor);
            }
        }

        // set up pre-functions -- OPTIONAL
        $pre = XMLUtil::getChildElement($result, 'pre-functions');
        if (null !== $pre) {
            $preFunctions = XMLUtil::getChildElements($pre, 'function');
            foreach ($preFunctions as $preFunction) {
                $functionDescriptor = DescriptorFactory::getFactory()->createFunctionDescriptor($preFunction);
                $functionDescriptor->setParent($this);
                $this->preFunctions->attach($functionDescriptor);
            }
        }

        // set up post-functions - OPTIONAL
        $post = XMLUtil::getChildElement($result, 'post-functions');
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
     * Возвращает предыдущий статус
     *
     * @return string
     */
    public function getOldStatus()
    {
        return $this->oldStatus;
    }

    /**
     * Устанавливает значение предыдующего статуса
     *
     * @param string $oldStatus
     *
     * @return $this
     */
    public function setOldStatus($oldStatus)
    {
        $this->oldStatus = (string)$oldStatus;

        return $this;
    }

    /**
     * Возвращает статус
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Устанавливает статус
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = (string)$status;

        return $this;
    }

    /**
     * Срок
     *
     * @return string
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @return int|null
     */
    public function getJoin()
    {
        return $this->join;
    }

    /**
     * @param int $join
     *
     * @return $this
     */
    public function setJoin($join)
    {
        $this->join = (integer)$join;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSplit()
    {
        return $this->split;
    }

    /**
     * @param int|null $split
     *
     * @return $this
     */
    public function setSplit($split)
    {
        $this->split = (integer)$split;

        return $this;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param int $step
     *
     * @return $this
     */
    public function setStep($step)
    {
        $this->step = (integer)$step;
        $this->hasStep = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param string $owner
     *
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = (string)$owner;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     *
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        /** @var ActionDescriptor $parent */
        $parent = $this->getParent();
        if ($parent instanceof ActionDescriptor) {
            $parentName = $parent->getName();
            if ($displayName === $parentName) {
                $this->displayName = null;
            }
        }
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return ValidatorDescriptor[]|SplObjectStorage
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param ValidatorDescriptor[]|SplObjectStorage $validators
     *
     * @return $this
     */
    public function setValidators($validators)
    {
        $this->validators = $validators;

        return $this;
    }

    /**
     * @return FunctionDescriptor[]|SplObjectStorage
     */
    public function getPreFunctions()
    {
        return $this->preFunctions;
    }

    /**
     * @return FunctionDescriptor[]|SplObjectStorage
     */
    public function getPostFunctions()
    {
        return $this->postFunctions;
    }
}
