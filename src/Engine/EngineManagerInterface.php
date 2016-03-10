<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\Workflow\WorkflowInterface;


/**
 * Interface EngineManagerInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface EngineManagerInterface
{
    /**
     * Конструктор менеджера движков
     *
     * AbstractEngine constructor.
     *
     * @param WorkflowInterface $wf
     */
    public function __construct(WorkflowInterface $wf);

    /**
     * Устанавливает менеджер workflow
     *
     * @return WorkflowInterface
     */
    public function getWorkflowManager();

    /**
     * Возвращает менеджер workflow
     *
     * @param WorkflowInterface $workflowManager
     *
     * @return $this
     */
    public function setWorkflowManager(WorkflowInterface $workflowManager);

    /**
     * Движок  отвечающий за переходы между шагами workflow
     *
     * @return TransitionInterface
     */
    public function getTransitionEngine();

    /**
     * Устанавливает движок  отвечающий за переходы между шагами workflow
     *
     * @param TransitionInterface $transitionEngine
     *
     * @return $this
     */
    public function setTransitionEngine(TransitionInterface $transitionEngine);

    /**
     * Возвращает движок  отвечающий за работу с условиями
     *
     * @return ConditionsInterface
     */
    public function getConditionsEngine();

    /**
     * Устанавливает движок  отвечающий за работу с условиями
     *
     * @param ConditionsInterface $conditionsEngine
     *
     * @return $this
     */
    public function setConditionsEngine($conditionsEngine);

    /**
     * Устанавливает движок  отвечающий за работу с аргументами
     *
     * @return ArgsInterface
     */
    public function getArgsEngine();

    /**
     * Возвращает движок отвечающий за работу с аргументами
     *
     * @param ArgsInterface $argsEngine
     *
     * @return $this
     */
    public function setArgsEngine(ArgsInterface $argsEngine);

    /**
     * Возвращает движок отвечающий за работу с функциями
     *
     * @return FunctionsInterface
     */
    public function getFunctionsEngine();

    /**
     * Устанавливает движок отвечающий за работу с функциями
     *
     * @param FunctionsInterface $functionsEngine
     *
     * @return $this
     */
    public function setFunctionsEngine(FunctionsInterface $functionsEngine);


    /**
     * Возвращает менеджер для работы с данными
     *
     * @return DataInterface
     */
    public function getDataEngine();

    /**
     * Устанавливает менеджер для работы с данными
     *
     * @param DataInterface $dataEngine
     *
     * @return $this
     */
    public function setDataEngine(DataInterface $dataEngine);

    /**
     * Возвращает движок для работы с процессами workflow
     *
     * @return EntryInterface
     */
    public function getEntryEngine();

    /**
     * Устанавливает движок для работы с процессами workflow
     *
     * @param EntryInterface $entryEngine
     *
     * @return $this
     */
    public function setEntryEngine(EntryInterface $entryEngine);
}
