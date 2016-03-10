<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Engine;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;

/**
 * Interface ArgsInterface
 *
 * @package OldTown\Workflow\Engine
 */
interface ArgsInterface extends EngineInterface
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
    public function prepareArgs(array $argsOriginal = [], TransientVarsInterface $transientVars, PropertySetInterface $ps);
}
