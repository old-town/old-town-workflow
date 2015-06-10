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
interface TypeInterface
{
    /**
     * Возвращает имя
     *
     * @return string
     */
    public function getType();

    /**
     * Устанавливает имя
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type);
}
