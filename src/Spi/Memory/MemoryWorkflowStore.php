<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Spi\Memory;

use OldTown\Workflow\Exception\ArgumentNotNumericException;
use OldTown\Workflow\Exception\InvalidWorkflowEntryException;
use OldTown\Workflow\Exception\NotFoundWorkflowEntryException;
use OldTown\Workflow\Query\FieldExpression;
use OldTown\Workflow\Query\NestedExpression;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use OldTown\Workflow\Spi\SimpleStep;
use OldTown\Workflow\Spi\SimpleWorkflowEntry;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\Exception\StoreException;
use DateTime;
use SplObjectStorage;
use OldTown\Workflow\Exception\InvalidArgumentException;

/**
 * Class MemoryWorkflowStore
 * @package OldTown\Workflow\Spi\Memory
 */
class MemoryWorkflowStore // implements WorkflowStoreInterface
{
    /**
     * @var SimpleWorkflowEntry[]
     */
    private static $entryCache = [];

    /**
     * @var SplObjectStorage[]|SimpleStep[]
     */
    private static $currentStepsCache = [];

    /**
     * @var SplObjectStorage[]|SimpleStep[]
     */
    private static $historyStepsCache = [];

    /**
     * @var array
     */
    private static $propertySetCache = [];

    /**
     * @var int
     */
    private static $globalEntryId = 1;

    /**
     * @var int
     */
    private static $globalStepId = 1;


    //~ Methods ////////////////////////////////////////////////////////////////

    /**
     * Устанавливает статус для сущности workflow с заданным id
     *
     * @param int $entryId
     * @param int $state
     *
     * @return $this
     * @throws StoreException
     * @throws NotFoundWorkflowEntryException
     * @throws ArgumentNotNumericException
     * @throws InvalidWorkflowEntryException
     */
    public function setEntryState($entryId, $state)
    {
        /** @var SimpleWorkflowEntry $theEntry */
        $theEntry = $this->findEntry($entryId);
        $theEntry->setState($state);
    }

    /**
     * Ищет сущность workflow с заданным id во внутреннем кеше
     *
     * @param int $entryId
     * @return WorkflowEntryInterface
     * @throws NotFoundWorkflowEntryException
     * @throws ArgumentNotNumericException
     * @throws InvalidWorkflowEntryException
     */
    public function findEntry($entryId)
    {
        if (!is_numeric($entryId)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $entryId);
            throw new ArgumentNotNumericException($errMsg);
        }

        if (!array_key_exists($entryId, static::$entryCache)) {
            $errMsg = sprintf('Не найдена сущность workflow с id %s', $entryId);
            throw new NotFoundWorkflowEntryException($errMsg);
        }

        $entry = static::$entryCache[$entryId];

        if (!$entry instanceof WorkflowEntryInterface) {
            $errMsg = sprintf('Сущность workflow должна реализовывать интерфейс %s', WorkflowEntryInterface::class);
            throw new InvalidWorkflowEntryException($errMsg);
        }


        return $entry;
    }

    /**
     * Создает экземпляр workflow
     *
     * @param string $workflowName
     * @return SimpleWorkflowEntry
     */
    public function createEntry($workflowName)
    {
        $id = static::$globalEntryId++;
        $entry = new SimpleWorkflowEntry($id, $workflowName, WorkflowEntryInterface::CREATED);
        static::$entryCache[$id] = $entry;

        return $entry;
    }

    /**
     * Создает новый шаг
     *
     * @param integer $entryId
     * @param integer $stepId
     * @param string $owner
     * @param DateTime $startDate
     * @param DateTime $dueDate
     * @param string $status
     * @param array $previousIds
     * @return SimpleStep
     */
    public function createCurrentStep($entryId, $stepId, $owner, DateTime $startDate, DateTime $dueDate, $status, array $previousIds = [])
    {
        $id = static::$globalStepId++;
        $step = new SimpleStep($id, $entryId, $stepId, 0, $owner, $startDate, $dueDate, null, $status, $previousIds, null);

        if (!array_key_exists($entryId, static::$currentStepsCache)) {
            $currentSteps = new SplObjectStorage();
            static::$currentStepsCache[$entryId] = $currentSteps;
        }


        static::$currentStepsCache[$entryId]->attach($step);

        return $step;
    }

    /**
     * Ищет текущий набор шагов для сущности workflow c заданным id
     *
     * @param Integer $entryId
     * @return SimpleStep[]|SplObjectStorage
     * @throws ArgumentNotNumericException
     */
    public function findCurrentSteps($entryId)
    {
        if (!is_numeric($entryId)) {
            $errMsg = sprintf('Аргумент должен быть числом. Актуальное значение %s', $entryId);
            throw new ArgumentNotNumericException($errMsg);
        }
        $entryId = (integer)$entryId;

        if (!array_key_exists($entryId, static::$currentStepsCache)) {
            $currentSteps = new SplObjectStorage();
            static::$currentStepsCache[$entryId] = $currentSteps;
        }

        return static::$currentStepsCache[$entryId];
    }

    /**
     * Пометить текущий шаг как выполненный
     *
     * @param StepInterface $step
     * @param integer $actionId
     * @param DateTime $finishDate
     * @param string $status
     * @param string $caller
     * @return null|SimpleStep
     *
     * @throws ArgumentNotNumericException
     */
    public function markFinished(StepInterface $step, $actionId, DateTime $finishDate, $status, $caller)
    {
        $entryId = $step->getEntryId();
        $currentSteps = $this->findCurrentSteps($entryId);

        foreach ($currentSteps as $theStep) {
            if ($theStep->getId() === $step->getId()) {
                $theStep->setStatus($status);
                $theStep->setActionId($actionId);
                $theStep->setFinishDate($finishDate);
                $theStep->setCaller($caller);

                return $theStep;
            }
        }

        return null;
    }

    /**
     * Сбрасывает внутренние кеш хранилища.
     */
    public static function reset()
    {
        static::$entryCache = [];
        static::$currentStepsCache = [];
        static::$historyStepsCache = [];
        static::$propertySetCache = [];
    }

    /**
     * Перенос шага в историю
     *
     * @param StepInterface $step
     * @return void
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function moveToHistory(StepInterface $step)
    {
        $entryId = $step->getEntryId();
        $currentSteps = $this->findCurrentSteps($entryId);

        if (!array_key_exists($entryId, static::$historyStepsCache)) {
            $historySteps = new SplObjectStorage();
            static::$historyStepsCache[$entryId] = $historySteps;
        }

        foreach ($currentSteps as $currentStep) {
            if ($step->getId() === $currentStep->getId()) {
                $currentSteps->detach($currentStep);
                foreach (static::$historyStepsCache[$entryId] as $historyStep) {
                    /** @var StepInterface $historyStep */
                    if ($historyStep->getId() === $step->getId()) {
                        static::$historyStepsCache[$entryId]->detach($historyStep);
                    }
                }

                static::$historyStepsCache[$entryId]->attach($currentStep);

                break;
            }
        }
    }

    /**
     * Поиск по истории шагов
     *
     * @param $entryId
     * @return SimpleStep[]|SplObjectStorage
     */
    public function findHistorySteps($entryId)
    {
        if (array_key_exists($entryId, static::$historyStepsCache)) {
            return static::$historyStepsCache[$entryId];
        }
        return new SplObjectStorage();
    }

    public function query(WorkflowExpressionQuery $query)
    {
        $results = [];

        foreach (static::$entryCache as $entryId => $mapEntry) {
            if ($this->queryInternal($entryId, $query)) {
                $results[$entryId] = $entryId;
            }
        }

        return $results;
    }

    private function queryInternal($entryId, WorkflowExpressionQuery $query)
    {
        $expression = $query->getExpression();

        if ($expression->isNested()) {
            return $this->checkNestedExpression($entryId, $expression);
        } else {
            return $this->checkExpression($entryId, $expression);
        }
    }

//

    private function checkExpression($entryId, FieldExpression $expression)
    {
        $value = $expression->getValue();
        $operator = $expression->getOperator();
        $field = $expression->getField();
        $context = $expression->getContext();

        $id = (integer)$entryId;

        if ($context === FieldExpression::ENTRY) {
            $theEntry = static::$entryCache[$id];

            if ($field === FieldExpression::NAME) {
                return $this->compareText($theEntry->getWorkflowName(), $value, $operator);
            }

            if ($field === FieldExpression::STATE) {
                //@fixme значение value может быть не только объектом
                $valueInt = (integer)$value;
                return $this->compareLong($valueInt, $theEntry->getState(), $operator);
            }

            throw new InvalidArgumentException('unknown field');
        }

        /** @var SplObjectStorage[]|SimpleStep[] $steps */
        $steps = [];

        if ($context === FieldExpression::CURRENT_STEPS) {
            $steps = array_key_exists($id, static::$currentStepsCache) ? static::$currentStepsCache[$id] : $steps;
        } else if ($context == FieldExpression::HISTORY_STEPS) {
            $steps = array_key_exists($id, static::$historyStepsCache) ? static::$historyStepsCache[$id] : $steps;
        } else {
            throw new InvalidArgumentException('unknown field context');
        }

        if (0 === count($steps)) {
            return false;
        }

        $expressionResult = false;

        switch ($field) {
            case FieldExpression::ACTION:

                //@fixme значение value может быть не только объектом
                $actionId = (integer)$value;

                foreach ($steps as $step) {
                    if ($this->compareLong($step->getActionId(), $actionId, $operator)) {
                        $expressionResult = true;

                        break;
                    }
                }


                break;

            case FieldExpression::CALLER:

                $caller = (String)$value;

                foreach ($steps as $step) {
                    if ($this->compareText($step->getCaller(), $caller, $operator)) {
                        $expressionResult = true;

                        break;
                    }
                }

                break;

            case FieldExpression::FINISH_DATE:
                if ($value instanceof DateTime) {
                    $finishDate = $value;
                    foreach ($steps as $step) {
                        if ($this->compareDate($step->getFinishDate(), $finishDate, $operator)) {
                            $expressionResult = true;

                            break;
                        }
                    }
                }

                break;

            case FieldExpression::OWNER:

                $owner = (string)$value;

                foreach ($steps as $step) {
                    if ($this->compareText($step->getOwner(), $owner, $operator)) {
                        $expressionResult = true;

                        break;
                    }
                }


                break;

            case FieldExpression::START_DATE:
                if ($value instanceof DateTime) {
                    $startDate = $value;
                    foreach ($steps as $step) {
                        if ($this->compareDate($step->getStartDate(), $startDate, $operator)) {
                            $expressionResult = true;

                            break;
                        }
                    }
                }

                break;

            case FieldExpression::STEP:

                //@fixme значение value может быть не только объектом
                $stepId = (integer)$value;

                foreach ($steps as $step) {
                    if ($this->compareLong($step->getStepId(), $stepId, $operator)) {
                        $expressionResult = true;

                        break;
                    }
                }

                break;

            case FieldExpression::STATUS:

                $status = (string)$value;

                foreach ($steps as $step) {
                    if ($this->compareText($step->getStatus(), $status, $operator)) {
                        $expressionResult = true;

                        break;
                    }
                }

                break;

            case FieldExpression::DUE_DATE:

                if ($value instanceof DateTime) {
                    $dueDate = $value;
                    foreach ($steps as $step) {
                        if ($this->compareDate($step->getDueDate(), $dueDate, $operator)) {
                            $expressionResult = true;

                            break;
                        }
                    }
                }


                break;
        }

        if ($expression->isNegate()) {
            return !$expressionResult;
        } else {
            return $expressionResult;
        }
    }

//
    private function checkNestedExpression($entryId, NestedExpression $nestedExpression)
    {
        $expressions = $nestedExpression->getExpressions();
        foreach ($expressions as $expression) {
            if ($expression->isNested()) {
                $expressionResult = $this->checkNestedExpression($entryId, $expression);
            } else {
                $expressionResult = $this->checkExpression($entryId, $expression);
            }

            if ($nestedExpression->getExpressionOperator() === NestedExpression::AND_OPERATOR) {
                if ($expressionResult === false) {
                    return $nestedExpression->isNegate();
                }
            } else if ($nestedExpression->getExpressionOperator() === NestedExpression::OR_OPERATOR) {
                if ($expressionResult === true) {
                    return !$nestedExpression->isNegate();
                }
            }
        }


        if ($nestedExpression->getExpressionOperator() === NestedExpression::AND_OPERATOR) {
            return !$nestedExpression->isNegate();
        } else if ($nestedExpression->getExpressionOperator() === NestedExpression::OR_OPERATOR) {
            return $nestedExpression->isNegate();
        }

        throw new InvalidArgumentException('unknown operator');
    }

    private function compareDate(DateTime $value1, DateTime $value2, $operator)
    {
        switch ($operator) {
            case FieldExpression::EQUALS:
                return $value1->diff($value2) == 0;

            case FieldExpression::NOT_EQUALS:
                return $value1->diff($value2) != 0;

            case FieldExpression::GT:
                return $value1->diff($value2) > 0;

            case FieldExpression::LT:
                return $value1->diff($value2) < 0;
        }

        throw new InvalidArgumentException('unknown field operator');
    }

    private function compareLong($value1, $value2, $operator)
    {
        switch ($operator) {
            case FieldExpression::EQUALS:
                return $value1 ==  $value2;

            case FieldExpression::NOT_EQUALS:
                return $value1 !=  $value2;

            case FieldExpression::GT:
                return $value1 >  $value2;

            case FieldExpression::LT:
                return $value1 <  $value2;
        }

        throw new InvalidArgumentException('unknown field operator');
    }

    private function compareText($value1, $value2, $operator)
    {
        switch ($operator) {
            case FieldExpression::EQUALS:
                return $value1 === $value2;

            case FieldExpression::NOT_EQUALS:
                return $value1 !== $value2;

            case FieldExpression::GT:
                return strlen($value1) > strlen($value2);

            case FieldExpression::LT:
                return strlen($value1) < strlen($value2);
        }

        throw new InvalidArgumentException('unknown field operator');
    }

}
