<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader\Traits;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
interface ArgsInterface
{
    /**
     * Возвращает аргументы
     *
     * @return array
     */
    public function getArgs();

    /**
     * Устанавливает аргумент
     *
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setArg($name, $value);

    /**
     * Устанавливает аргумент
     *
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return string
     */
    public function getArg($name, $defaultValue = null);

}
