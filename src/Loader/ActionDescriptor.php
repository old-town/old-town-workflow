<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Loader\Traits;
use SplObjectStorage;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ActionDescriptor extends AbstractDescriptor implements Traits\NameInterface
{
    use Traits\NameTrait, Traits\IdTrait;

    /**
     * @var ConditionalResultDescriptor[]|SplObjectStorage
     */
    protected $conditionalResults;

    /**
     * @var FunctionDescriptor[]|SplObjectStorage
     */
    protected $postFunctions;

    /**
     * @var FunctionDescriptor[]|SplObjectStorage
     */
    protected $preFunctions;

    /**
     * @var ValidatorDescriptor[]|SplObjectStorage
     */
    protected $validators;

    /**
     * @var array
     */
    protected $metaAttributes = [];

    /**
     * @var RestrictionDescriptor
     */
    protected $restriction;

    /**
     * @var ResultDescriptor
     */
    protected $unconditionalResult;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var bool
     */
    protected $autoExecute = false;

    /**
     * @var bool
     */
    protected $common = false;

    /**
     * @var bool
     */
    protected $finish = false;

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        $this->validators = new SplObjectStorage();
        $this->preFunctions = new SplObjectStorage();
        $this->conditionalResults = new SplObjectStorage();
        $this->postFunctions = new SplObjectStorage();
        if (null !== $element) {
            $this->init($element);
        }

        parent::__construct($element);
    }

    /**
     * @param DOMElement $action
     *
     * @return void
     */
    protected function init(DOMElement $action)
    {
        $this->parseId($action);
        $this->parseName($action);

        if ($action->hasAttribute('view')) {
            $this->view = XmlUtil::getRequiredAttributeValue($action, 'view');
        }

        if ($action->hasAttribute('auto')) {
            $autoValue = XmlUtil::getRequiredAttributeValue($action, 'auto');
            $auto = strtolower($autoValue);
            $this->autoExecute = 'true' === $auto;
        }

        if ($action->hasAttribute('finish')) {
            $finishValue = XmlUtil::getRequiredAttributeValue($action, 'finish');
            $finish = strtolower($finishValue);
            $this->finish = 'true' === $finish;
        }

        $metaElements = XmlUtil::getChildElements($action, 'meta');
        foreach ($metaElements as $meta) {

            $value = XmlUtil::getText($meta);
            $name = XmlUtil::getRequiredAttributeValue($meta, 'name');

            $this->metaAttributes[$name] = $value;
        }

        // set up validators -- OPTIONAL
        $v = XMLUtil::getChildElement($action, 'validators');
        if (null !== $v) {
            $validators = XMLUtil::getChildElements($v, 'validator');
            foreach ($validators as $validator) {
                $validatorDescriptor = DescriptorFactory::getFactory()->createValidatorDescriptor($validator);
                $validatorDescriptor->setParent($this);
                $this->validators->attach($validatorDescriptor);

            }
        }

        // set up pre-functions -- OPTIONAL
        $pre = XMLUtil::getChildElement($action, 'pre-functions');
        if (null !== $pre) {
            $preFunctions = XMLUtil::getChildElements($pre, 'function');
            foreach ($preFunctions as $preFunction) {
                $functionDescriptor = DescriptorFactory::getFactory()->createFunctionDescriptor($preFunction);
                $functionDescriptor->setParent($this);
                $this->preFunctions->attach($functionDescriptor);

            }
        }

        // set up results - REQUIRED
        $resultsElememt = XMLUtil::getChildElement($action, 'results');
        $results = XMLUtil::getChildElements($resultsElememt, 'result');
        foreach ($results as $result) {
            $conditionalResultDescriptor = new ConditionalResultDescriptor($result);
            $conditionalResultDescriptor->setParent($this);
            $this->conditionalResults->attach($conditionalResultDescriptor);

        }

        $unconditionalResult = XMLUtil::getChildElement($resultsElememt, 'unconditional-result');
        if (null !== $unconditionalResult) {
            $this->unconditionalResult = DescriptorFactory::getFactory()->createResultDescriptor($unconditionalResult);
            $this->unconditionalResult->setParent($this);
        }


        // set up post-functions - OPTIONAL
        $post = XMLUtil::getChildElement($action, 'post-functions');
        if (null !== $post) {
            $postFunctions = XMLUtil::getChildElements($post, 'function');
            foreach ($postFunctions as $postFunction) {
                $functionDescriptor = DescriptorFactory::getFactory()->createFunctionDescriptor($postFunction);
                $functionDescriptor->setParent($this);
                $this->postFunctions->attach($functionDescriptor);

            }
        }

        // set up restrict-to - OPTIONAL
        $restrictElement = XMLUtil::getChildElement($action, 'restrict-to');
        if (null !== $restrictElement) {
            $this->restriction = new RestrictionDescriptor($restrictElement);

            if (null === $this->restriction->getConditionsDescriptor()) {
                $this->restriction = null;
            } else {
                $this->restriction->setParent($this);
            }

        }

    }

    /**
     * @return boolean
     */
    public function getAutoExecute()
    {
        return $this->autoExecute;
    }

    /**
     * @param boolean $autoExecute
     *
     * @return $this
     */
    public function setAutoExecute($autoExecute)
    {
        $this->autoExecute = (boolean)$autoExecute;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCommon()
    {
        return $this->common;
    }

    /**
     * @param boolean $common
     *
     * @return $this
     */
    public function setCommon($common)
    {
        $this->common = (boolean)$common;

        return $this;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = (string)$view;

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
     * @return ResultDescriptor
     */
    public function getUnconditionalResult()
    {
        return $this->unconditionalResult;
    }

    /**
     * @param ResultDescriptor $unconditionalResult
     *
     * @return $this
     */
    public function setUnconditionalResult(ResultDescriptor $unconditionalResult)
    {
        $this->unconditionalResult = $unconditionalResult;

        return $this;
    }

    /**
     * @return RestrictionDescriptor
     */
    public function getRestriction()
    {
        return $this->restriction;
    }

    /**
     * @param RestrictionDescriptor $restriction
     *
     * @return $this
     */
    public function setRestriction(RestrictionDescriptor $restriction)
    {
        $this->restriction = $restriction;

        return $this;
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
     * @return boolean
     */
    public function isFinish()
    {
        return $this->finish;
    }

    /**
     * @param boolean $finish
     *
     * @return $this
     */
    public function setFinish($finish)
    {
        $this->finish = (boolean)$finish;

        return $this;
    }

    /**
     * @return ConditionalResultDescriptor[]|SplObjectStorage
     */
    public function getConditionalResults()
    {
        return $this->conditionalResults;
    }


}
