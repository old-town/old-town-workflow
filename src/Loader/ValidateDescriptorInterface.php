<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

/**
 * Interface ValidateDescriptorInterface
 * @package OldTown\Workflow\Loader
 */
interface  ValidateDescriptorInterface
{
    /**
     * Валидация дескриптора
     *
     * @return void
     */
    public function validate();
}
