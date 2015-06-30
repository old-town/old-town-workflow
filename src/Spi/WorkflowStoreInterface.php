<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Spi;

use OldTown\Workflow\Exception\StoreException;
use OldTown\PropertySet\PropertySetInterface;
use DateTime;
use OldTown\Workflow\Query\WorkflowExpressionQuery;
use SplObjectStorage;

/**
 * Интерфейся для подключаемых workflow настроенных в xml файлах
 *
 * @package OldTown\Workflow\Spi
 */
interface WorkflowStoreInterface
{
    /**
         * Устанавливает состояние для текущего workflow
         *
         * @param integer $entryId id workflow
         * @param integer $state id состояния в которое переводится сущность workflow
         * @return void
         * @throws StoreException
         */
        public function setEntryState($entryId, $state);

        /**
         * Возвращает PropertySet that связанный с данным экземпляром workflow
         * @param integer $entryId id workflow
         * @return PropertySetInterface
         * @throws StoreException
         */
        public function getPropertySet($entryId);

        /**
         * Persists a step with the given parameters.
         *
         * @param integer $entryId id workflow
         * @param integer $stepId id шага
         * @param string $owner владелец шага
         * @param DateTime $startDate дата когда произошел старт шага
         * @param DateTime $dueDate
         * @param string $status статус
         * @param integer[] $previousIds Id предыдущих шагов
         * @throws StoreException
         * @return StepInterface объект описывающий сохраненный шаг workflow
         */
        public function createCurrentStep($entryId, $stepId, $owner, DateTime $startDate, DateTime $dueDate, $status, array $previousIds = []);

        /**
         * Создает новую сущность workflow (не инициазированную)
         *
         * @param string $workflowName имя workflow, используемого для данной сущности
         * @throws StoreException
         * @return WorkflowEntryInterface
         */
        public function createEntry($workflowName);

        /**
         * Возвращает список шагов
         *
         * @param integer $entryId id экземпляра workflow
         * @throws StoreException
         * @return StepInterface[]
         */
        public function findCurrentSteps($entryId);

        /**
         * Загрузить экземпляр workflow
         *
         * @param integer $entryId
         * @throws \OldTown\Workflow\Exception\StoreException
         * @return WorkflowEntryInterface
         */
        public function findEntry($entryId);

        /**
         * Получения истории шагов
         *
         * @param entryId
         * @throws StoreException
         * @return StepInterface[]|SplObjectStorage
         */
        public function findHistorySteps($entryId);

        /**
         * Вызывается один раз, при инициализации хранилища
         *
         * @param array $props
         * @throws StoreException
         */
        public function init(array $props = []);

        /**
         * Помечате выбранный шаг, как выполенный
         *
         * @param StepInterface $step шаг который хоим пометить как выполненный
         * @param integer $actionId Действие которое привело к окончанию шага
         * @param DateTime $finishDate дата когда шаг был финиширован
         * @param string $status
         * @param string $caller Информация о том, кто вызвал шаг что бы его закончить
         * @throws StoreException
         * @return StepInterface finished step
         */
        public function markFinished(StepInterface $step, $actionId, DateTime $finishDate, $status, $caller);

        /**
         * Called when a step is finished and can be moved to workflow history.
         *
         * @param StepInterface $step шаг, который переносится в историю
         * @return $this
         */
        public function moveToHistory(StepInterface $step);

        /**
         * @param WorkflowExpressionQuery $query
         * @throws StoreException
         * @return array
         */
        public function query(WorkflowExpressionQuery $query);
}
