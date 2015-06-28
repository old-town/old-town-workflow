<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;

/**
 * Interface FunctionProviderRemoteInterface
 *
 * @package OldTown\Workflow
 */
interface FunctionProviderInterface
{
    /**
     *
     * @param array $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     * @return
     */
    public function execute(array $transientVars = [], array $args = [], PropertySetInterface $ps);
}
