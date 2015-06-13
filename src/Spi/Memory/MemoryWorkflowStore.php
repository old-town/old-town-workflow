<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Spi\Memory;

use OldTown\Workflow\Exception\ArgumentNotNumericException;
use OldTown\Workflow\Exception\InvalidWorkflowEntryException;
use OldTown\Workflow\Exception\NotFoundWorkflowEntryException;
use OldTown\Workflow\Spi\SimpleStep;
use OldTown\Workflow\Spi\SimpleWorkflowEntry;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\Exception\StoreException;
use DateTime;
use SplObjectStorage;

/**
 * Class MemoryWorkflowStore
 * @package OldTown\Workflow\Spi\Memory
 */
class MemoryWorkflowStore // implements WorkflowStoreInterface
{
    /**
     * @var array
     */
    private static $entryCache = [];

    /**
     * @var SplObjectStorage[]|SimpleStep[]
     */
    private static $currentStepsCache = [];

    /**
     * @var array
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
//
//    /**
//     * Reset the MemoryWorkflowStore so it doesn't have any information.
//     * Useful when testing and you don't want the MemoryWorkflowStore to
//     * have old data in it.
//     */
//    public static void reset() {
//entryCache.clear();
//        currentStepsCache.clear();
//        historyStepsCache.clear();
//        propertySetCache.clear();
//    }
//
//
//
//    public List findCurrentSteps(long entryId) {
//    List currentSteps = (List) currentStepsCache.get(new Long(entryId));
//
//        if (currentSteps == null) {
//            currentSteps = new ArrayList();
//            currentStepsCache.put(new Long(entryId), currentSteps);
//        }
//
//        return new ArrayList(currentSteps);
//    }
//
//
//
//    public List findHistorySteps(long entryId) {
//    List historySteps = (List) historyStepsCache.get(new Long(entryId));
//
//        if (historySteps == null) {
//            historySteps = new ArrayList();
//            historyStepsCache.put(new Long(entryId), historySteps);
//        }
//
//        return new ArrayList(historySteps);
//    }
//
//    public void init(Map props) {
//}
//
//    public Step markFinished(Step step, int actionId, Date finishDate, String status, String caller) {
//    List currentSteps = (List) currentStepsCache.get(new Long(step.getEntryId()));
//
//        for (Iterator iterator = currentSteps.iterator(); iterator.hasNext();) {
//        SimpleStep theStep = (SimpleStep) iterator.next();
//
//            if (theStep.getId() == step.getId()) {
//                theStep.setStatus(status);
//                theStep.setActionId(actionId);
//                theStep.setFinishDate(finishDate);
//                theStep.setCaller(caller);
//
//                return theStep;
//            }
//        }
//
//        return null;
//    }
//
//    public void moveToHistory(Step step) {
//    List currentSteps = (List) currentStepsCache.get(new Long(step.getEntryId()));
//
//        List historySteps = (List) historyStepsCache.get(new Long(step.getEntryId()));
//
//        if (historySteps == null) {
//            historySteps = new ArrayList();
//            historyStepsCache.put(new Long(step.getEntryId()), historySteps);
//        }
//
//        SimpleStep simpleStep = (SimpleStep) step;
//
//        for (Iterator iterator = currentSteps.iterator(); iterator.hasNext();) {
//        Step currentStep = (Step) iterator.next();
//
//            if (simpleStep.getId() == currentStep.getId()) {
//                iterator.remove();
//                historySteps.add(0, simpleStep);
//
//                break;
//            }
//        }
//    }
//
//    public List query(WorkflowQuery query) {
//    ArrayList results = new ArrayList();
//
//        for (Iterator iterator = entryCache.entrySet().iterator();
//             iterator.hasNext();) {
//        Map.Entry mapEntry = (Map.Entry) iterator.next();
//            Long entryId = (Long) mapEntry.getKey();
//
//            if (query(entryId, query)) {
//                results.add(entryId);
//            }
//        }
//
//        return results;
//    }
//
//    public List query(WorkflowExpressionQuery query) {
//    ArrayList results = new ArrayList();
//
//        for (Iterator iterator = entryCache.entrySet().iterator();
//             iterator.hasNext();) {
//        Map.Entry mapEntry = (Map.Entry) iterator.next();
//            Long entryId = (Long) mapEntry.getKey();
//
//            if (query(entryId.longValue(), query)) {
//                results.add(entryId);
//            }
//        }
//
//        return results;
//    }
//
//    private boolean checkExpression(long entryId, FieldExpression expression) {
//    Object value = expression.getValue();
//        int operator = expression.getOperator();
//        int field = expression.getField();
//        int context = expression.getContext();
//
//        Long id = new Long(entryId);
//
//        if (context == FieldExpression.ENTRY) {
//            SimpleWorkflowEntry theEntry = (SimpleWorkflowEntry) entryCache.get(id);
//
//            if (field == FieldExpression.NAME) {
//                return this.compareText(theEntry.getWorkflowName(), (String) value, operator);
//            }
//
//            if (field == FieldExpression.STATE) {
//                return this.compareLong(DataUtil.getInt((Integer) value), theEntry.getState(), operator);
//            }
//
//            throw new InvalidParameterException("unknown field");
//        }
//
//        List steps;
//
//        if (context == FieldExpression.CURRENT_STEPS) {
//            steps = (List) currentStepsCache.get(id);
//        } else if (context == FieldExpression.HISTORY_STEPS) {
//            steps = (List) historyStepsCache.get(id);
//        } else {
//            throw new InvalidParameterException("unknown field context");
//        }
//
//        if (steps == null) {
//            return false;
//        }
//
//        boolean expressionResult = false;
//
//        switch (field) {
//            case FieldExpression.ACTION:
//
//                long actionId = DataUtil.getInt((Integer) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareLong(step.getActionId(), actionId, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//
//            case FieldExpression.CALLER:
//
//                String caller = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareText(step.getCaller(), caller, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//
//            case FieldExpression.FINISH_DATE:
//
//                Date finishDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareDate(step.getFinishDate(), finishDate, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//
//            case FieldExpression.OWNER:
//
//                String owner = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareText(step.getOwner(), owner, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//
//            case FieldExpression.START_DATE:
//
//                Date startDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareDate(step.getStartDate(), startDate, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//
//            case FieldExpression.STEP:
//
//                int stepId = DataUtil.getInt((Integer) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareLong(step.getStepId(), stepId, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//
//            case FieldExpression.STATUS:
//
//                String status = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareText(step.getStatus(), status, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//
//            case FieldExpression.DUE_DATE:
//
//                Date dueDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//                SimpleStep step = (SimpleStep) iterator.next();
//
//                if (this.compareDate(step.getDueDate(), dueDate, operator)) {
//                    expressionResult = true;
//
//                    break;
//                }
//            }
//
//            break;
//        }
//
//        if (expression.isNegate()) {
//            return !expressionResult;
//        } else {
//            return expressionResult;
//        }
//    }
//
//    private boolean checkNestedExpression(long entryId, NestedExpression nestedExpression) {
//    for (int i = 0; i < nestedExpression.getExpressionCount(); i++) {
//        boolean expressionResult;
//            Expression expression = nestedExpression.getExpression(i);
//
//            if (expression.isNested()) {
//                expressionResult = this.checkNestedExpression(entryId, (NestedExpression) expression);
//            } else {
//                expressionResult = this.checkExpression(entryId, (FieldExpression) expression);
//            }
//
//            if (nestedExpression.getExpressionOperator() == NestedExpression.AND) {
//                if (expressionResult == false) {
//                    return nestedExpression.isNegate();
//                }
//            } else if (nestedExpression.getExpressionOperator() == NestedExpression.OR) {
//                if (expressionResult == true) {
//                    return !nestedExpression.isNegate();
//                }
//            }
//        }
//
//        if (nestedExpression.getExpressionOperator() == NestedExpression.AND) {
//            return !nestedExpression.isNegate();
//        } else if (nestedExpression.getExpressionOperator() == NestedExpression.OR) {
//            return nestedExpression.isNegate();
//        }
//
//        throw new InvalidParameterException("unknown operator");
//    }
//
//    private boolean compareDate(Date value1, Date value2, int operator) {
//    switch (operator) {
//        case FieldExpression.EQUALS:
//            return value1.compareTo(value2) == 0;
//
//        case FieldExpression.NOT_EQUALS:
//            return value1.compareTo(value2) != 0;
//
//        case FieldExpression.GT:
//            return (value1.compareTo(value2) > 0);
//
//        case FieldExpression.LT:
//            return value1.compareTo(value2) < 0;
//    }
//
//    throw new InvalidParameterException("unknown field operator");
//}
//
//    private boolean compareLong(long value1, long value2, int operator) {
//    switch (operator) {
//        case FieldExpression.EQUALS:
//            return value1 == value2;
//
//        case FieldExpression.NOT_EQUALS:
//            return value1 != value2;
//
//        case FieldExpression.GT:
//            return value1 > value2;
//
//        case FieldExpression.LT:
//            return value1 < value2;
//    }
//
//    throw new InvalidParameterException("unknown field operator");
//}
//
//    private boolean compareText(String value1, String value2, int operator) {
//    switch (operator) {
//        case FieldExpression.EQUALS:
//            return TextUtils.noNull(value1).equals(value2);
//
//        case FieldExpression.NOT_EQUALS:
//            return !TextUtils.noNull(value1).equals(value2);
//
//        case FieldExpression.GT:
//            return TextUtils.noNull(value1).compareTo(value2) > 0;
//
//        case FieldExpression.LT:
//            return TextUtils.noNull(value1).compareTo(value2) < 0;
//    }
//
//    throw new InvalidParameterException("unknown field operator");
//}
//
//    private boolean query(Long entryId, WorkflowQuery query) {
//    if (query.getLeft() == null) {
//        return queryBasic(entryId, query);
//    } else {
//        int operator = query.getOperator();
//            WorkflowQuery left = query.getLeft();
//            WorkflowQuery right = query.getRight();
//
//            switch (operator) {
//                case WorkflowQuery.AND:
//                    return query(entryId, left) && query(entryId, right);
//
//                case WorkflowQuery.OR:
//                    return query(entryId, left) || query(entryId, right);
//
//                case WorkflowQuery.XOR:
//                    return query(entryId, left) ^ query(entryId, right);
//            }
//        }
//
//    return false;
//}
//
//    private boolean query(long entryId, WorkflowExpressionQuery query) {
//    Expression expression = query.getExpression();
//
//        if (expression.isNested()) {
//            return this.checkNestedExpression(entryId, (NestedExpression) expression);
//        } else {
//            return this.checkExpression(entryId, (FieldExpression) expression);
//        }
//    }
//
//    private boolean queryBasic(Long entryId, WorkflowQuery query) {
//    // the query object is a comparison
//    Object value = query.getValue();
//        int operator = query.getOperator();
//        int field = query.getField();
//        int type = query.getType();
//
//        switch (operator) {
//            case WorkflowQuery.EQUALS:
//                return queryEquals(entryId, field, type, value);
//
//            case WorkflowQuery.NOT_EQUALS:
//                return queryNotEquals(entryId, field, type, value);
//
//            case WorkflowQuery.GT:
//                return queryGreaterThan(entryId, field, type, value);
//
//            case WorkflowQuery.LT:
//                return queryLessThan(entryId, field, type, value);
//        }
//
//        return false;
//    }
//
//    private boolean queryEquals(Long entryId, int field, int type, Object value) {
//    List steps;
//
//    if (type == WorkflowQuery.CURRENT) {
//        steps = (List) currentStepsCache.get(entryId);
//    } else {
//        steps = (List) historyStepsCache.get(entryId);
//    }
//
//    if (steps == null) {
//        return false;
//    }
//
//    switch (field) {
//        case WorkflowQuery.ACTION:
//
//            long actionId = DataUtil.getInt((Integer) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getActionId() == actionId) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.CALLER:
//
//            String caller = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getCaller()).equals(caller)) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.FINISH_DATE:
//
//            Date finishDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (finishDate.equals(step.getFinishDate())) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.OWNER:
//
//            String owner = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getOwner()).equals(owner)) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.START_DATE:
//
//            Date startDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (startDate.equals(step.getStartDate())) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STEP:
//
//            int stepId = DataUtil.getInt((Integer) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (stepId == step.getStepId()) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STATUS:
//
//            String status = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getStatus()).equals(status)) {
//                    return true;
//                }
//            }
//
//            return false;
//    }
//
//    return false;
//}
//
//    private boolean queryGreaterThan(Long entryId, int field, int type, Object value) {
//    List steps;
//
//    if (type == WorkflowQuery.CURRENT) {
//        steps = (List) currentStepsCache.get(entryId);
//    } else {
//        steps = (List) historyStepsCache.get(entryId);
//    }
//
//    switch (field) {
//        case WorkflowQuery.ACTION:
//
//            long actionId = DataUtil.getLong((Long) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getActionId() > actionId) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.CALLER:
//
//            String caller = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getCaller()).compareTo(caller) > 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.FINISH_DATE:
//
//            Date finishDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getFinishDate().compareTo(finishDate) > 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.OWNER:
//
//            String owner = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getOwner()).compareTo(owner) > 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.START_DATE:
//
//            Date startDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getStartDate().compareTo(startDate) > 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STEP:
//
//            int stepId = DataUtil.getInt((Integer) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getStepId() > stepId) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STATUS:
//
//            String status = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getStatus()).compareTo(status) > 0) {
//                    return true;
//                }
//            }
//
//            return false;
//    }
//
//    return false;
//}
//
//    private boolean queryLessThan(Long entryId, int field, int type, Object value) {
//    List steps;
//
//    if (type == WorkflowQuery.CURRENT) {
//        steps = (List) currentStepsCache.get(entryId);
//    } else {
//        steps = (List) historyStepsCache.get(entryId);
//    }
//
//    switch (field) {
//        case WorkflowQuery.ACTION:
//
//            long actionId = DataUtil.getLong((Long) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getActionId() < actionId) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.CALLER:
//
//            String caller = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getCaller()).compareTo(caller) < 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.FINISH_DATE:
//
//            Date finishDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getFinishDate().compareTo(finishDate) < 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.OWNER:
//
//            String owner = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getOwner()).compareTo(owner) < 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.START_DATE:
//
//            Date startDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getStartDate().compareTo(startDate) < 0) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STEP:
//
//            int stepId = DataUtil.getInt((Integer) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getStepId() < stepId) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STATUS:
//
//            String status = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (TextUtils.noNull(step.getStatus()).compareTo(status) < 0) {
//                    return true;
//                }
//            }
//
//            return false;
//    }
//
//    return false;
//}
//
//    private boolean queryNotEquals(Long entryId, int field, int type, Object value) {
//    List steps;
//
//    if (type == WorkflowQuery.CURRENT) {
//        steps = (List) currentStepsCache.get(entryId);
//    } else {
//        steps = (List) historyStepsCache.get(entryId);
//    }
//
//    switch (field) {
//        case WorkflowQuery.ACTION:
//
//            long actionId = DataUtil.getLong((Long) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (step.getActionId() != actionId) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.CALLER:
//
//            String caller = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (!TextUtils.noNull(step.getCaller()).equals(caller)) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.FINISH_DATE:
//
//            Date finishDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (!finishDate.equals(step.getFinishDate())) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.OWNER:
//
//            String owner = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (!TextUtils.noNull(step.getOwner()).equals(owner)) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.START_DATE:
//
//            Date startDate = (Date) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (!startDate.equals(step.getStartDate())) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STEP:
//
//            int stepId = DataUtil.getInt((Integer) value);
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (stepId != step.getStepId()) {
//                    return true;
//                }
//            }
//
//            return false;
//
//        case WorkflowQuery.STATUS:
//
//            String status = (String) value;
//
//            for (Iterator iterator = steps.iterator(); iterator.hasNext();) {
//            SimpleStep step = (SimpleStep) iterator.next();
//
//                if (!TextUtils.noNull(step.getStatus()).equals(status)) {
//                    return true;
//                }
//            }
//
//            return false;
//    }
//
//    return false;
//}


//public PropertySet getPropertySet(long entryId) {
//    PropertySet ps = (PropertySet) propertySetCache.get(new Long(entryId));
//
//        if (ps == null) {
//            ps = PropertySetManager.getInstance("memory", null);
//            propertySetCache.put(new Long(entryId), ps);
//        }
//
//        return ps;
//    }
}
