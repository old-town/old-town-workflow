<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Loader\RegisterDescriptor;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use Traversable;
use SplObjectStorage;

/**
 * Interface DataInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface DataInterface extends EngineInterface
{
    /**
     * Преобразование данных в массив
     *
     * @param $data
     *
     * @return array
     */
    public function convertDataInArray($data);

    /**
     * @param WorkflowEntryInterface $entry
     * @param TransientVarsInterface $transientVars
     * @param array|Traversable|RegisterDescriptor[]|SplObjectStorage $registersStorage
     * @param integer $actionId
     * @param array|Traversable $currentSteps
     * @param PropertySetInterface $ps
     *
     *
     * @return TransientVarsInterface
     */
    public function populateTransientMap(WorkflowEntryInterface $entry, TransientVarsInterface $transientVars, $registersStorage, $actionId = null, $currentSteps, PropertySetInterface $ps);

    /**
     * Проверка того что данные могут быть использованы в цикле
     *
     * @param $data
     */
    public function validateIterateData($data);
}
