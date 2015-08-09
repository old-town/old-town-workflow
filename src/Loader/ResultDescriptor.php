<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;
use SplObjectStorage;
use DOMDocument;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 *
 * @method ActionDescriptor getParent()
 */
class ResultDescriptor extends AbstractDescriptor implements ValidateDescriptorInterface, WriteXmlInterface
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
     * Если флаг установлен в true, то не запускаем инициализацию дескриптора для элемента
     *
     * @var bool
     */
    protected $flagNotExecuteInit = false;

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        $validators = new SplObjectStorage();
        $this->setValidators($validators);

        $this->preFunctions = new SplObjectStorage();
        $this->postFunctions = new SplObjectStorage();

        parent::__construct($element);

        if (null !== $element && !$this->flagNotExecuteInit) {
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
        $oldStatus = XmlUtil::getRequiredAttributeValue($result, 'old-status');
        $this->setOldStatus($oldStatus);
        if ($result->hasAttribute('status')) {
            $status = XmlUtil::getRequiredAttributeValue($result, 'status');
            $this->setStatus($status);
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
            $this->setDisplayName($displayName);
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
                return $this;
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
    public function setValidators(SplObjectStorage $validators)
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

    /**
     * Вывод информации о функциях пост обработки
     *
     * @param DOMDocument $dom
     * @return DOMElement|null
     * @throws InvalidWriteWorkflowException
     */
    protected function printPostFunctions(DOMDocument $dom)
    {
        $postFunctions = $this->getPostFunctions();
        if ($postFunctions->count() > 0) {
            $postFunctionsElements = $dom->createElement('post-functions');
            foreach ($postFunctions as $function) {
                try {
                    $functionElement = $function->writeXml($dom);
                } catch (\Exception $e) {
                    $errMsg  = 'Ошибка сохранения workflow';
                    throw new InvalidWriteWorkflowException($errMsg, $e->getCode(), $e);
                }

                $postFunctionsElements->appendChild($functionElement);
            }

            return $postFunctionsElements;
        }

        return null;
    }

    /**
     * Вывод информации о функциях пред обработки
     *
     * @param DOMDocument $dom
     * @return DOMElement|null
     *
     * @throws InvalidWriteWorkflowException
     */
    protected function printPreFunctions(DOMDocument $dom)
    {
        $preFunctions = $this->getPreFunctions();
        if ($preFunctions->count() > 0) {
            $preFunctionsElements = $dom->createElement('pre-functions');
            foreach ($preFunctions as $function) {
                try {
                    $functionElement = $function->writeXml($dom);
                } catch (\Exception $e) {
                    $errMsg  = 'Ошибка сохранения workflow';
                    throw new InvalidWriteWorkflowException($errMsg, $e->getCode(), $e);
                }
                $preFunctionsElements->appendChild($functionElement);
            }

            return $preFunctionsElements;
        }

        return null;
    }


    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement
     * @throws InvalidDescriptorException
     * @throws InvalidWriteWorkflowException
     */
    public function writeXml(DOMDocument $dom = null)
    {
        if (null === $dom) {
            $errMsg = 'Не передан DOMDocument';
            throw new InvalidWriteWorkflowException($errMsg);
        }
        $descriptor = $dom->createElement('unconditional-result');

        if ($this->hasId()) {
            $id = $this->getId();
            $descriptor->setAttribute('id', $id);
        }

        $dueDate = $this->getDueDate();
        if (null !== $dueDate && is_string($dueDate) && strlen($dueDate) > 0) {
            $descriptor->setAttribute('due-date', $dueDate);
        }

        $oldStatus = $this->getOldStatus();
        if (null === $oldStatus) {
            $errMsg = 'Некорректное значение для атрибута old-status';
            throw new InvalidDescriptorException($errMsg);
        }
        $descriptor->setAttribute('old-status', $oldStatus);



        $join = $this->getJoin();
        $split = $this->getSplit();
        if (null !== $join && 0 !== $join) {
            $descriptor->setAttribute('join', $join);
        } elseif (null !== $split && 0 !== $split) {
            $descriptor->setAttribute('split', $split);
        } else {
            $status = $this->getStatus();
            if (null === $status) {
                $errMsg = 'Некорректное значение для атрибута status';
                throw new InvalidDescriptorException($errMsg);
            }
            $descriptor->setAttribute('status', $status);

            $step = $this->getStep();
            if (null === $step) {
                $errMsg = 'Некорректное значение для атрибута step';
                throw new InvalidDescriptorException($errMsg);
            }
            $descriptor->setAttribute('step', $step);

            $owner = $this->getOwner();
            if (null !== $owner && is_string($owner) && strlen($owner) > 0) {
                $descriptor->setAttribute('owner', $owner);
            }

            $displayName = $this->getDisplayName();
            if (null !== $displayName && is_string($displayName) && strlen($displayName) > 0) {
                $descriptor->setAttribute('display-name', $displayName);
            }
        }

        $validators = $this->getValidators();
        if ($validators->count() > 0) {
            $validatorsDescriptor = $dom->createElement('validators');
            $descriptor->appendChild($validatorsDescriptor);

            foreach ($validators as $validator) {
                $validatorElement = $validator->writeXml($dom);
                $validatorsDescriptor->appendChild($validatorElement);
            }
        }

        $preFunctionsElement = $this->printPreFunctions($dom);
        if (null !== $preFunctionsElement) {
            $descriptor->appendChild($preFunctionsElement);
        }

        $postFunctionsElement = $this->printPostFunctions($dom);
        if (null !== $postFunctionsElement) {
            $descriptor->appendChild($postFunctionsElement);
        }

        return $descriptor;
    }


    /**
     * Валидация дескриптора
     *
     * @return void
     * @throws InvalidWorkflowDescriptorException
     */
    public function validate()
    {
        $preFunctions = $this->getPreFunctions();
        $postFunctions = $this->getPostFunctions();
        $validators = $this->getValidators();

        ValidationHelper::validate($preFunctions);
        ValidationHelper::validate($postFunctions);
        ValidationHelper::validate($validators);

        $split = $this->getSplit();
        $join = $this->getJoin();

        $parent = $this->getParent();
        if ((0 === $split) && (0 === $join) && !($parent instanceof ActionDescriptor && ($parent->isFinish()))) {
            $errMsg = '';
            $id = (integer)$this->getId();
            if ($id > 0) {
                $errMsg .= sprintf('#%s', $id);
            }
            $errMsg .= 'Не имеет ни split ни join вложенных дескрипторов';

            if ($this->hasStep) {
                $errMsg .= ' для следующего шага';
                throw new InvalidWorkflowDescriptorException($errMsg);
            }

            $status = $this->getStatus();
            if (!$status) {
                $errMsg .= ' для статуса';
                throw new InvalidWorkflowDescriptorException($errMsg);
            }
        }
    }
}
