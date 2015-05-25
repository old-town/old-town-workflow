<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util;

use OldTown\PropertySet\PropertySetInterface;

/**
 * Interface VariableResolverInterface
 *
 * @package OldTown\Workflow\Util
 */
interface  VariableResolverInterface
{
    /**
     *
     * @param string $s
     * @param array $transientVars
     * @param PropertySetInterface $ps
     *
     * @return object
     */
    public function passesCondition($s, array $transientVars = [], PropertySetInterface $ps);
}
