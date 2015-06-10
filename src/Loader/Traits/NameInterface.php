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
interface NameInterface
{
    /**
     * Возвращает имя
     *
     * @return string
     */
    public function getName();

    /**
     * Устанавливает имя
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name);
}
