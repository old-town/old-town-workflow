<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\Workflow\WorkflowInterface;

/**
 * Class EngineManager
 *
 * @package OldTown\Workflow\Engine
 */
class EngineManager implements EngineManagerInterface
{
    use WorkflowManagerTrait;

    /**
     * @var TransitionInterface
     */
    protected $transitionEngine;

    /**
     * @var ConditionsInterface
     */
    protected $conditionsEngine;

    /**
     * @var ArgsInterface
     */
    protected $argsEngine;

    /**
     * @var FunctionsInterface
     */
    protected $functionsEngine;

    /**
     * @var DataInterface
     */
    protected $dataEngine;

    /**
     * @var EntryInterface
     */
    protected $entryEngine;

    /**
     * Конструктор абстрактного движка
     *
     * AbstractEngine constructor.
     *
     * @param WorkflowInterface $wf
     */
    public function __construct(WorkflowInterface $wf)
    {
        $this->setWorkflowManager($wf);
    }

    /**
     * Движок  отвечающий за переходы между шагами workflow
     *
     * @return TransitionInterface
     */
    public function getTransitionEngine()
    {
        if ($this->transitionEngine) {
            return $this->transitionEngine;
        }
        $wfManager = $this->getWorkflowManager();
        $this->transitionEngine = new Transition($wfManager);

        return $this->transitionEngine;
    }

    /**
     * Устанавливает движок  отвечающий за переходы между шагами workflow
     *
     * @param TransitionInterface $transitionEngine
     *
     * @return $this
     */
    public function setTransitionEngine(TransitionInterface $transitionEngine)
    {
        $this->transitionEngine = $transitionEngine;

        return $this;
    }

    /**
     * Возвращает движок  отвечающий за работу с условиями
     *
     * @return ConditionsInterface
     */
    public function getConditionsEngine()
    {
        if ($this->conditionsEngine) {
            return $this->conditionsEngine;
        }
        $wfManager = $this->getWorkflowManager();
        $this->conditionsEngine = new Conditions($wfManager);

        return $this->conditionsEngine;
    }

    /**
     * Устанавливает движок  отвечающий за работу с условиями
     *
     * @param ConditionsInterface $conditionsEngine
     *
     * @return $this
     */
    public function setConditionsEngine($conditionsEngine)
    {
        $this->conditionsEngine = $conditionsEngine;

        return $this;
    }

    /**
     * Устанавливает движок  отвечающий за работу с аргументами
     *
     * @return ArgsInterface
     */
    public function getArgsEngine()
    {
        if ($this->argsEngine) {
            return $this->argsEngine;
        }
        $wfManager = $this->getWorkflowManager();
        $this->argsEngine = new Args($wfManager);

        return $this->argsEngine;
    }

    /**
     * Возвращает движок отвечающий за работу с аргументами
     *
     * @param ArgsInterface $argsEngine
     *
     * @return $this
     */
    public function setArgsEngine(ArgsInterface $argsEngine)
    {
        $this->argsEngine = $argsEngine;

        return $this;
    }

    /**
     * Возвращает движок отвечающий за работу с функциями
     *
     * @return FunctionsInterface
     */
    public function getFunctionsEngine()
    {
        if ($this->functionsEngine) {
            return $this->functionsEngine;
        }
        $wfManager = $this->getWorkflowManager();
        $this->functionsEngine = new Functions($wfManager);

        return $this->functionsEngine;
    }

    /**
     * Устанавливает движок отвечающий за работу с функциями
     *
     * @param FunctionsInterface $functionsEngine
     *
     * @return $this
     */
    public function setFunctionsEngine(FunctionsInterface $functionsEngine)
    {
        $this->functionsEngine = $functionsEngine;

        return $this;
    }

    /**
     * Возвращает движок для работы с данными
     *
     * @return DataInterface
     */
    public function getDataEngine()
    {
        if ($this->dataEngine) {
            return $this->dataEngine;
        }
        $wfManager = $this->getWorkflowManager();
        $this->dataEngine = new Data($wfManager);


        return $this->dataEngine;
    }

    /**
     * Устанавливает движок для работы с данными
     *
     * @param DataInterface $dataEngine
     *
     * @return $this
     */
    public function setDataEngine(DataInterface $dataEngine)
    {
        $this->dataEngine = $dataEngine;

        return $this;
    }

    /**
     * Возвращает движок для работы с процессами workflow
     *
     * @return EntryInterface
     */
    public function getEntryEngine()
    {
        if ($this->entryEngine) {
            return $this->entryEngine;
        }
        $wfManager = $this->getWorkflowManager();
        $this->entryEngine = new Entry($wfManager);



        return $this->entryEngine;
    }

    /**
     * Устанавливает движок для работы с процессами workflow
     *
     * @param EntryInterface $entryEngine
     *
     * @return $this
     */
    public function setEntryEngine(EntryInterface $entryEngine)
    {
        $this->entryEngine = $entryEngine;

        return $this;
    }
}
