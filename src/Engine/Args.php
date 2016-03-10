<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;


/**
 * Class Args
 *
 * @package OldTown\Workflow\Engine
 */
class Args extends AbstractEngine implements ArgsInterface
{
    /**
     * Подготавливает аргументы.
     *
     * @param array                  $argsOriginal
     *
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface   $ps
     *
     * @return array
     */
    public function prepareArgs(array $argsOriginal = [], TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $args = [];
        $variableResolver = $this->getWorkflowManager()->getConfiguration()->getVariableResolver();
        foreach ($argsOriginal as $key => $value) {
            $translateValue = $variableResolver->translateVariables($value, $transientVars, $ps);
            $args[$key] = $translateValue;
        }

        return $args;
    }
}
