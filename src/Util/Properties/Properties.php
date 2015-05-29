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
class  Properties implements PropertiesInterface
{

    /**
     * @var array
     */
    protected $storage = [];

    /**
     * @param string       $key
     * @param null|string  $defaultValue
     *
     * @return mixed
     */
    public function getProperty($key, $defaultValue = null)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }

        return $defaultValue;
    }


    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setProperty($key, $value)
    {
        $this->storage[$key] = $value;

        return $this;
    }
}
