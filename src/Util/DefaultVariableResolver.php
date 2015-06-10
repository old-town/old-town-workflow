<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util;

use OldTown\PropertySet\PropertySetInterface;

/**
 * Class DefaultVariableResolver
 *
 * @package OldTown\Workflow\Util
 */
class  DefaultVariableResolver implements VariableResolverInterface
{
    /**
     *
     * @param string $s
     * @param array $transientVars
     * @param PropertySetInterface $ps
     *
     * @return object
     */
    public function translateVariables($s, array $transientVars = [], PropertySetInterface $ps)
    {
    }
}
