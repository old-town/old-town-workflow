<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util\Properties;

/**
 * Interface PropertiesInterface
 *
 * @package OldTown\Workflow\Util\Properties
 */
interface  PropertiesInterface
{
    /**
     * @param string       $key
     * @param null|string  $defaultValue
     *
     * @return mixed
     */
    public function getProperty($key, $defaultValue = null);


    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setProperty($key, $value);
}
