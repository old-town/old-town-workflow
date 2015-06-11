<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader\Traits;

use DOMElement;

/**
 * Interface CustomArgInterface
 *
 * @package OldTown\Workflow\Loader\Traits
 */
interface CustomArgInterface
{
    /**
     * Генерирует значение аргумента
     *
     * @param string     $key
     * @param string     $value
     *
     * @param DOMElement $argElement
     *
     * @return string
     */
    public function buildArgValue($key, $value, DOMElement $argElement);

    /**
     * @param string $key
     * @param string $value
     *
     * @return boolean
     */
    public function flagUseCustomArgWriter($key, $value);
}
