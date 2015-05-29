<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Config;


/**
 * Interface ConfigurationInterface
 *
 * @package OldTown\Workflow\Config
 */
class  DefaultConfiguration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $workflowName
     * @param object $layout
     * @return void
     */
    public function setLayout($workflowName, $layout)
    {

    }

    /**
     * @param string $workflowName
     * @return Object
     */
    public function getLayout($workflowName)
    {
        return null;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function isModifiable($name)
    {
        return false;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return "";
    }
}
