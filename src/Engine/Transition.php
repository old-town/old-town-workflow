<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\InvalidInputException;
use OldTown\Workflow\Exception\StoreException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\JoinNodes;
use OldTown\Workflow\Loader\ActionDescriptor;
use OldTown\Workflow\Loader\ResultDescriptor;
use OldTown\Workflow\Loader\ValidatorDescriptor;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Spi\StepInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use SplObjectStorage;
use DateTime;
use OldTown\Workflow\Exception\InvalidArgumentException;


/**
 * Class Transition
 *
 * @package OldTown\Workflow
 */
class Transition extends AbstractEngine implements TransitionInterface
{
    /**
     *
     * @var array
     */
    protected $stateCache = [];

    /**
     * Переход между двумя статусами
     *
     * @param WorkflowEntryInterface $entry
     * @param SplObjectStorage|StepInterface[] $currentSteps
     * @param WorkflowStoreInterface $store
     * @param WorkflowDescriptor $wf
     * @param ActionDescriptor $action
     * @param TransientVarsInterface $transientVars
     * @param TransientVarsInterface $inputs
     * @param PropertySetInterface $ps
     *
     * @return boolean
     *
     * @throws InternalWorkflowException
     */
    public function transitionWorkflow(WorkflowEntryInterface $entry, SplObjectStorage $currentSteps, WorkflowStoreInterface $store, WorkflowDescriptor $wf, ActionDescriptor $action, TransientVarsInterface $transientVars, TransientVarsInterface $inputs, PropertySetInterface $ps)
    {
        try {
            $step = $this->getCurrentStep($wf, $action->getId(), $currentSteps, $transientVars, $ps);

            $validators = $action->getValidators();
            if ($validators->count() > 0) {
                $this->verifyInputs($validators, $transientVars, $ps);
            }

            $workflowManager = $this->getWorkflowManager();
            $engineManager = $workflowManager->getEngineManager();
            $functionsEngine = $engineManager->getFunctionsEngine();

            if (null !== $step) {
                $stepPostFunctions = $wf->getStep($step->getStepId())->getPostFunctions();
                foreach ($stepPostFunctions as $function) {
                    $functionsEngine->executeFunction($function, $transientVars, $ps);
                }
            }

            $preFunctions = $action->getPreFunctions();
            foreach ($preFunctions as $preFunction) {
                $functionsEngine->executeFunction($preFunction, $transientVars, $ps);
            }

            $conditionalResults = $action->getConditionalResults();
            $extraPreFunctions = null;
            $extraPostFunctions = null;

            $theResult = null;



            $currentStepId = null !== $step ? $step->getStepId()  : -1;


            $conditionsEngine = $engineManager->getConditionsEngine();
            $log = $workflowManager->getLog();
            $context = $workflowManager->getContext();

            foreach ($conditionalResults as $conditionalResult) {
                if ($conditionsEngine->passesConditionsWithType(null, $conditionalResult->getConditions(), $transientVars, $ps, $currentStepId)) {
                    $theResult = $conditionalResult;

                    $validatorsStorage = $conditionalResult->getValidators();
                    if ($validatorsStorage->count() > 0) {
                        $this->verifyInputs($validatorsStorage, $transientVars, $ps);
                    }

                    $extraPreFunctions = $conditionalResult->getPreFunctions();
                    $extraPostFunctions = $conditionalResult->getPostFunctions();

                    break;
                }
            }


            if (null ===  $theResult) {
                $theResult = $action->getUnconditionalResult();
                $this->verifyInputs($theResult->getValidators(), $transientVars, $ps);
                $extraPreFunctions = $theResult->getPreFunctions();
                $extraPostFunctions = $theResult->getPostFunctions();
            }

            $logMsg = sprintf('theResult=%s %s', $theResult->getStep(), $theResult->getStatus());
            $log->debug($logMsg);


            if ($extraPreFunctions && $extraPreFunctions->count() > 0) {
                foreach ($extraPreFunctions as $function) {
                    $functionsEngine->executeFunction($function, $transientVars, $ps);
                }
            }

            $split = $theResult->getSplit();
            $join = $theResult->getJoin();
            if (null !== $split && 0 !== $split) {
                $splitDesc = $wf->getSplit($split);
                $results = $splitDesc->getResults();
                $splitPreFunctions = [];
                $splitPostFunctions = [];

                foreach ($results as $resultDescriptor) {
                    if ($resultDescriptor->getValidators()->count() > 0) {
                        $this->verifyInputs($resultDescriptor->getValidators(), $transientVars, $ps);
                    }

                    foreach ($resultDescriptor->getPreFunctions() as $function) {
                        $splitPreFunctions[] = $function;
                    }
                    foreach ($resultDescriptor->getPostFunctions() as $function) {
                        $splitPostFunctions[] = $function;
                    }
                }

                foreach ($splitPreFunctions as $function) {
                    $functionsEngine->executeFunction($function, $transientVars, $ps);
                }

                if (!$action->isFinish()) {
                    $moveFirst = true;

                    foreach ($results as $resultDescriptor) {
                        $moveToHistoryStep = null;

                        if ($moveFirst) {
                            $moveToHistoryStep = $step;
                        }

                        $previousIds = [];

                        if (null !== $step) {
                            $previousIds[] = $step->getStepId();
                        }

                        $this->createNewCurrentStep($resultDescriptor, $entry, $store, $action->getId(), $moveToHistoryStep, $previousIds, $transientVars, $ps);
                        $moveFirst = false;
                    }
                }


                foreach ($splitPostFunctions as $function) {
                    $functionsEngine->executeFunction($function, $transientVars, $ps);
                }
            } elseif (null !== $join && 0 !== $join) {
                $joinDesc = $wf->getJoin($join);
                $oldStatus = $theResult->getOldStatus();
                $caller = $context->getCaller();
                if (null !== $step) {
                    $step = $store->markFinished($step, $action->getId(), new DateTime(), $oldStatus, $caller);
                } else {
                    $errMsg = 'Invalid step';
                    throw new InternalWorkflowException($errMsg);
                }


                $store->moveToHistory($step);

                /** @var StepInterface[] $joinSteps */
                $joinSteps = [];
                $joinSteps[] = $step;

                $joinSteps = $this->buildJoinsSteps($currentSteps, $step, $wf, $join, $joinSteps);

                $historySteps = $store->findHistorySteps($entry->getId());

                $joinSteps = $this->buildJoinsSteps($historySteps, $step, $wf, $join, $joinSteps);


                $jn = new JoinNodes($joinSteps);
                $transientVars['jn'] = $jn;


                if ($conditionsEngine->passesConditionsWithType(null, $joinDesc->getConditions(), $transientVars, $ps, 0)) {
                    $joinResult = $joinDesc->getResult();

                    $joinResultValidators = $joinResult->getValidators();
                    if ($joinResultValidators->count() > 0) {
                        $this->verifyInputs($joinResultValidators, $transientVars, $ps);
                    }

                    foreach ($joinResult->getPreFunctions() as $function) {
                        $functionsEngine->executeFunction($function, $transientVars, $ps);
                    }

                    $previousIds = [];
                    $i = 1;

                    foreach ($joinSteps as  $currentJoinStep) {
                        if (!$historySteps->contains($currentJoinStep) && $currentJoinStep->getId() !== $step->getId()) {
                            $store->moveToHistory($step);
                        }

                        $previousIds[$i] = $currentJoinStep->getId();
                    }

                    if (!$action->isFinish()) {
                        $previousIds[0] = $step->getId();
                        $theResult = $joinDesc->getResult();

                        $this->createNewCurrentStep($theResult, $entry, $store, $action->getId(), null, $previousIds, $transientVars, $ps);
                    }

                    foreach ($joinResult->getPostFunctions() as $function) {
                        $functionsEngine->executeFunction($function, $transientVars, $ps);
                    }
                }
            } else {
                $previousIds = [];

                if (null !== $step) {
                    $previousIds[] = $step->getId();
                }

                if (!$action->isFinish()) {
                    $this->createNewCurrentStep($theResult, $entry, $store, $action->getId(), $step, $previousIds, $transientVars, $ps);
                }
            }

            if ($extraPostFunctions && $extraPostFunctions->count() > 0) {
                foreach ($extraPostFunctions as $function) {
                    $functionsEngine->executeFunction($function, $transientVars, $ps);
                }
            }

            if (WorkflowEntryInterface::COMPLETED !== $entry->getState() && null !== $wf->getInitialAction($action->getId())) {
                $workflowManager->changeEntryState($entry->getId(), WorkflowEntryInterface::ACTIVATED);
            }

            if ($action->isFinish()) {
                $entryEngine = $engineManager->getEntryEngine();
                $entryEngine->completeEntry($action, $entry->getId(), $workflowManager->getCurrentSteps($entry->getId()), WorkflowEntryInterface::COMPLETED);
                return true;
            }

            $availableAutoActions = $this->getAvailableAutoActions($entry->getId(), $inputs);

            if (count($availableAutoActions) > 0) {
                $workflowManager->doAction($entry->getId(), $availableAutoActions[0], $inputs);
            }

            return false;
        } catch (\Exception $e) {
            throw new InternalWorkflowException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param ResultDescriptor       $theResult
     * @param WorkflowEntryInterface $entry
     * @param WorkflowStoreInterface $store
     * @param integer                $actionId
     * @param StepInterface          $currentStep
     * @param array                  $previousIds
     * @param TransientVarsInterface                  $transientVars
     * @param PropertySetInterface   $ps
     *
     * @return StepInterface
     *
     * @throws InternalWorkflowException
     * @throws StoreException
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws WorkflowException
     */
    protected function createNewCurrentStep(
        ResultDescriptor $theResult,
        WorkflowEntryInterface $entry,
        WorkflowStoreInterface $store,
        $actionId,
        StepInterface $currentStep = null,
        array $previousIds = [],
        TransientVarsInterface $transientVars,
        PropertySetInterface $ps
    ) {
        $workflowManager = $this->getWorkflowManager();
        $context = $workflowManager->getContext();


        try {
            $nextStep = $theResult->getStep();

            if (-1 === $nextStep) {
                if (null !== $currentStep) {
                    $nextStep = $currentStep->getStepId();
                } else {
                    $errMsg = 'Неверный аргумент. Новый шаг является таким же как текущий. Но текущий шаг не указан';
                    throw new StoreException($errMsg);
                }
            }

            $owner = $theResult->getOwner();

            $logMsg = sprintf(
                'Результат: stepId=%s, status=%s, owner=%s, actionId=%s, currentStep=%s',
                $nextStep,
                $theResult->getStatus(),
                $owner,
                $actionId,
                null !== $currentStep ? $currentStep->getId() : 0
            );

            $log = $workflowManager->getLog();


            $log->debug($logMsg);

            $variableResolver = $workflowManager->getConfiguration()->getVariableResolver();

            if (null !== $owner) {
                $o = $variableResolver->translateVariables($owner, $transientVars, $ps);
                $owner = null !== $o ? (string)$o : null;
            }


            $oldStatus = $theResult->getOldStatus();
            $oldStatus = (string)$variableResolver->translateVariables($oldStatus, $transientVars, $ps);

            $status = $theResult->getStatus();
            $status = (string)$variableResolver->translateVariables($status, $transientVars, $ps);


            if (null !== $currentStep) {
                $store->markFinished($currentStep, $actionId, new DateTime(), $oldStatus, $context->getCaller());
                $store->moveToHistory($currentStep);
            }

            $startDate = new DateTime();
            $dueDate = null;

            $theResultDueDate = (string)$theResult->getDueDate();
            $theResultDueDate = trim($theResultDueDate);
            if (strlen($theResultDueDate) > 0) {
                $dueDateObject = $variableResolver->translateVariables($theResultDueDate, $transientVars, $ps);

                if ($dueDateObject instanceof DateTime) {
                    $dueDate = $dueDateObject;
                } elseif (is_string($dueDateObject)) {
                    $dueDate = new DateTime($dueDate);
                } elseif (is_numeric($dueDateObject)) {
                    $dueDate = DateTime::createFromFormat('U', $dueDateObject);
                    if (false === $dueDate) {
                        $errMsg = 'Invalid due date conversion';
                        throw new InternalWorkflowException($errMsg);
                    }
                }
            }

            $newStep = $store->createCurrentStep($entry->getId(), $nextStep, $owner, $startDate, $dueDate, $status, $previousIds);
            $transientVars['createdStep'] =  $newStep;

            if (null === $currentStep && 0 === count($previousIds)) {
                $currentSteps = [];
                $currentSteps[] = $newStep;
                $transientVars['currentSteps'] =  $currentSteps;
            }

            if (! $transientVars->offsetExists('descriptor')) {
                $errMsg = 'Ошибка при получение дескриптора workflow из transientVars';
                throw new InternalWorkflowException($errMsg);
            }

            /** @var WorkflowDescriptor $descriptor */
            $descriptor = $transientVars['descriptor'];
            $step = $descriptor->getStep($nextStep);

            if (null === $step) {
                $errMsg = sprintf('Шаг #%s не найден', $nextStep);
                throw new WorkflowException($errMsg);
            }

            $preFunctions = $step->getPreFunctions();

            $functionsEngine = $workflowManager->getEngineManager()->getFunctionsEngine();
            foreach ($preFunctions as $function) {
                $functionsEngine->executeFunction($function, $transientVars, $ps);
            }
        } catch (WorkflowException $e) {
            $context->setRollbackOnly();
            /** @var WorkflowException $e */
            throw $e;
        }
    }


    /**
     *
     * Возвращает текущий шаг
     *
     * @param WorkflowDescriptor $wfDesc
     * @param integer $actionId
     * @param StepInterface[]|SplObjectStorage $currentSteps
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     *
     * @return StepInterface
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws InternalWorkflowException
     */
    protected function getCurrentStep(WorkflowDescriptor $wfDesc, $actionId, SplObjectStorage $currentSteps, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        if (1 === $currentSteps->count()) {
            $currentSteps->rewind();
            return $currentSteps->current();
        }


        foreach ($currentSteps as $step) {
            $stepId = $step->getId();
            $action = $wfDesc->getStep($stepId)->getAction($actionId);

            if ($this->isActionAvailable($action, $transientVars, $ps, $stepId)) {
                return $step;
            }
        }

        return null;
    }


    /**
     * @param $validatorsStorage
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     *
     * @throws InvalidInputException
     * @throws WorkflowException
     */
    protected function verifyInputs($validatorsStorage, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $workflowManager = $this->getWorkflowManager();
        $engineManager = $workflowManager->getEngineManager();
        $argsEngine = $engineManager->getArgsEngine();
        $dataEngine = $engineManager->getDataEngine();


        $validators = $dataEngine->convertDataInArray($validatorsStorage);


        $resolver = $workflowManager->getResolver();

        /** @var ValidatorDescriptor[] $validators */
        foreach ($validators as $input) {
            if (null !== $input) {
                $type = $input->getType();
                $argsOriginal = $input->getArgs();

                $args = $argsEngine->prepareArgs($argsOriginal, $transientVars, $ps);


                $validator = $resolver->getValidator($type, $args);

                if (null === $validator) {
                    $workflowManager->getContext()->setRollbackOnly();
                    $errMsg = 'Ошибка при загрузке валидатора';
                    throw new WorkflowException($errMsg);
                }

                try {
                    $validator->validate($transientVars, $args, $ps);
                } catch (InvalidInputException $e) {
                    /** @var  InvalidInputException $e*/
                    throw $e;
                } catch (\Exception $e) {
                    $workflowManager->getContext()->setRollbackOnly();

                    if ($e instanceof WorkflowException) {
                        /** @var  WorkflowException $e*/
                        throw $e;
                    }

                    throw new WorkflowException($e->getMessage(), $e->getCode(), $e);
                }
            }
        }
    }


    /**
     * Подготавливает данные о шагах используемых в объеденение
     *
     * @param StepInterface[]|SplObjectStorage    $steps
     * @param StepInterface      $step
     * @param WorkflowDescriptor $wf
     * @param integer            $join
     *
     * @param array              $joinSteps
     *
     * @return array
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    protected function buildJoinsSteps($steps, StepInterface $step, WorkflowDescriptor $wf, $join, array $joinSteps = [])
    {
        foreach ($steps as $currentStep) {
            if ($currentStep->getId() !== $step->getId()) {
                $stepDesc = $wf->getStep($currentStep->getStepId());

                if ($stepDesc->resultsInJoin($join)) {
                    $joinSteps[] = $currentStep;
                }
            }
        }

        return $joinSteps;
    }


    /**
     * @param       $id
     * @param TransientVarsInterface $inputs
     *
     * @return array
     */
    protected function getAvailableAutoActions($id, TransientVarsInterface $inputs)
    {
        $workflowManager = $this->getWorkflowManager();
        try {
            $configurations = $workflowManager->getConfiguration();
            $store = $configurations->getWorkflowStore();

            $entry = $store->findEntry($id);

            if (null === $entry) {
                $errMsg = sprintf(
                    'Нет сущности workflow c id %s',
                    $id
                );
                throw new InvalidArgumentException($errMsg);
            }


            if (WorkflowEntryInterface::ACTIVATED !== $entry->getState()) {
                $logMsg = sprintf('--> состояние %s', $entry->getState());
                $workflowManager->getLog()->debug($logMsg);
                return [0];
            }

            $wf = $configurations->getWorkflow($entry->getWorkflowName());

            $l = [];
            $ps = $store->getPropertySet($id);
            $transientVars = $inputs;
            $currentSteps = $store->findCurrentSteps($id);

            $workflowManager->getEngineManager()->getDataEngine()->populateTransientMap($entry, $transientVars, $wf->getRegisters(), 0, $currentSteps, $ps);

            $globalActions = $wf->getGlobalActions();

            $l = $this->buildListIdsAvailableActions($globalActions, $transientVars, $ps, $l);

            foreach ($currentSteps as $step) {
                $availableAutoActionsForStep = $this->getAvailableAutoActionsForStep($wf, $step, $transientVars, $ps);
                foreach ($availableAutoActionsForStep as $v) {
                    $l[] = $v;
                }
            }

            $l = array_unique($l);

            return $l;
        } catch (\Exception $e) {
            $errMsg = 'Ошибка при проверке доступных действий';
            $workflowManager->getLog()->error($errMsg, [$e]);
        }

        return [];
    }


    /**
     * @param WorkflowDescriptor   $wf
     * @param StepInterface        $step
     * @param TransientVarsInterface                $transientVars
     * @param PropertySetInterface $ps
     *
     * @return array
     *
     * @throws \OldTown\Workflow\Exception\ArgumentNotNumericException
     * @throws InternalWorkflowException
     * @throws WorkflowException
     */
    protected function getAvailableAutoActionsForStep(WorkflowDescriptor $wf, StepInterface $step, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $l = [];
        $s = $wf->getStep($step->getStepId());

        if (null === $s) {
            $msg = sprintf('getAvailableAutoActionsForStep вызвана с несуществующим id %s', $step->getStepId());
            $this->getWorkflowManager()->getLog()->debug($msg);
            return $l;
        }


        $actions = $s->getActions();
        if (null === $actions || 0 === $actions->count()) {
            return $l;
        }

        $l = $this->buildListIdsAvailableActions($actions, $transientVars, $ps, $l);

        return $l;
    }


    /**
     * Подготавливает список id действий в workflow
     *
     * @param ActionDescriptor[]|SplObjectStorage     $actions
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface   $ps
     * @param array                  $storage
     *
     * @return array
     *
     * @throws InternalWorkflowException
     * @throws WorkflowException
     */
    protected function buildListIdsAvailableActions($actions, TransientVarsInterface $transientVars, PropertySetInterface $ps, array $storage = [])
    {
        foreach ($actions as $action) {
            if ($action instanceof ActionDescriptor) {
                $errMsg = sprintf('Invalid workflow action. Action not implement %s', ActionDescriptor::class);
                throw new InternalWorkflowException($errMsg);
            }
            $transientVars['actionId'] = $action->getId();

            if ($action->getAutoExecute() && $this->isActionAvailable($action, $transientVars, $ps, 0)) {
                $storage[] = $action->getId();
            }
        }

        return $storage;
    }


    /**
     *
     * @param ActionDescriptor|null $action
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     * @param $stepId
     *
     * @return boolean
     *
     * @throws InternalWorkflowException
     */
    public function isActionAvailable(ActionDescriptor $action = null, TransientVarsInterface $transientVars, PropertySetInterface $ps, $stepId)
    {
        if (null === $action) {
            return false;
        }

        $result = null;
        $actionHash = spl_object_hash($action);

        $result = array_key_exists($actionHash, $this->stateCache) ? $this->stateCache[$actionHash] : $result;

        $wf = $this->getWorkflowDescriptorForAction($action);

        $conditionsEngine = $this->getWorkflowManager()->getEngineManager()->getConditionsEngine();
        if (null === $result) {
            $restriction = $action->getRestriction();
            $conditions = null;

            if (null !== $restriction) {
                $conditions = $restriction->getConditionsDescriptor();
            }

            $result = $conditionsEngine->passesConditionsByDescriptor($wf->getGlobalConditions(), $transientVars, $ps, $stepId)
                && $conditionsEngine->passesConditionsByDescriptor($conditions, $transientVars, $ps, $stepId);

            $this->stateCache[$actionHash] = $result;
        }


        $result = (boolean)$result;

        return $result;
    }



    /**
     *
     * По дейсвтию получаем дексрипторв workflow
     *
     * @param ActionDescriptor $action
     *
     * @return WorkflowDescriptor
     *
     * @throws InternalWorkflowException
     */
    private function getWorkflowDescriptorForAction(ActionDescriptor $action)
    {
        $objWfd = $action;

        $count = 0;
        while (!$objWfd instanceof WorkflowDescriptor || null === $objWfd) {
            $objWfd = $objWfd->getParent();

            $count++;
            if ($count > 10) {
                $errMsg = 'Ошибка при получение WorkflowDescriptor';
                throw new InternalWorkflowException($errMsg);
            }
        }

        return $objWfd;
    }
}
