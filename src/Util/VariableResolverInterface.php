<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;

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
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface $ps
     *
     * @return mixed
     */
    public function translateVariables($s, TransientVarsInterface $transientVars, PropertySetInterface $ps);
}
