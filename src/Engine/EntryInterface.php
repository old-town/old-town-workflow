<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\Workflow\Loader\ActionDescriptor;
use Traversable;

/**
 * Interface EntryInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface EntryInterface extends EngineInterface
{
    /**
     * @param ActionDescriptor $action
     * @param                  $id
     * @param array|Traversable $currentSteps
     * @param                  $state
     *
     * @return void
     *
     */
    public function completeEntry(ActionDescriptor $action = null, $id, $currentSteps, $state);
}
