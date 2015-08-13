<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;
use SplObjectStorage;
use DOMDocument;


/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ConditionalResultDescriptor extends ResultDescriptor
{
    /**
     * @var ConditionsDescriptor[]|SplObjectStorage
     */
    protected $conditions;

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        $this->conditions = new SplObjectStorage();

        $this->flagNotExecuteInit = true;
        parent::__construct($element);
        $this->flagNotExecuteInit = false;

        if (null !== $element) {
            $this->init($element);
        }
    }

    /**
     * @param DOMElement $conditionalResult
     *
     * @return void
     */
    protected function init(DOMElement $conditionalResult)
    {
        parent::init($conditionalResult);

        $conditionNodes = XMLUtil::getChildElements($conditionalResult, 'conditions');

        foreach ($conditionNodes as $condition) {
            $conditionDescriptor = DescriptorFactory::getFactory()->createConditionsDescriptor($condition);
            $conditionDescriptor->setParent($this);
            $this->conditions->attach($conditionDescriptor);
        }
    }

    /**
     * @return ConditionsDescriptor[]|SplObjectStorage
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     *
     *
     * @return string
     * @throws InvalidWorkflowDescriptorException
     * @throws InternalWorkflowException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function getDestination()
    {
        /** @var WorkflowDescriptor $desc */
        $desc = null;
        $sName = '';

        //action
        $parent = $this->getParent();
        if (!$parent instanceof AbstractDescriptor) {
            $errMsg = sprintf(
                'Родитель должен реализовывать %s',
                AbstractDescriptor::class
            );
            throw new InvalidWorkflowDescriptorException($errMsg);
        }

        //step
        $actionDesc = $parent->getParent();
        if (null !== $actionDesc) {
            $desc = $actionDesc->getParent();
        }

        $join = $this->getJoin();
        $split = $this->getSplit();
        if (null !== $join && 0 !== $join) {
            $result  = sprintf('join #%s', $join);
            return $result;
        } elseif (null !== $split && 0 !== $split) {
            $result  = sprintf('split #%s', $split);
            return $result;
        } else {
            $step = $this->getStep();
            if ($desc !== null) {
                /** @var WorkflowDescriptor $desc */

                $stepDescriptor = $desc->getStep($step);

                if (!$stepDescriptor instanceof StepDescriptor) {
                    $errMsg = sprintf(
                        'Дескриптор шалаг должен реализовывать %s',
                        StepDescriptor::class
                    );
                    throw new InvalidWorkflowDescriptorException($errMsg);
                }
                $sName = $stepDescriptor->getName();
            }

            $result  = sprintf('step #%s [%s]', $step, $sName);
            return $result;
        }
    }


    /**
     * Валидация дескриптора
     *
     * @return void
     * @throws InvalidWorkflowDescriptorException
     * @throws InternalWorkflowException
     */
    public function validate()
    {
        parent::validate();

        $conditions =  $this->getConditions();
        if (0 === $conditions->count()) {
            $actionDescriptor = $this->getParent();
            if (!$actionDescriptor instanceof ActionDescriptor) {
                $errMsg = sprintf(
                    'Родитель должен реализовывать %s',
                    ActionDescriptor::class
                );
                throw new InvalidWorkflowDescriptorException($errMsg);
            }

            $errMsg = sprintf(
                'Результат условия от %s к %s должны иметь по крайней мере одну условие',
                $actionDescriptor->getName(),
                $this->getDestination()
            );
            throw new InvalidWorkflowDescriptorException($errMsg);
        }

        ValidationHelper::validate($conditions);
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

        $descriptor = $dom->createElement('result');

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


        foreach ($this->getConditions() as $condition) {
            $conditionElement = $condition->writeXml($dom);
            if ($conditionElement) {
                $descriptor->appendChild($conditionElement);
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
}
