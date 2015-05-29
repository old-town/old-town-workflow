<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Util\Properties\PropertiesInterface;
use OldTown\Workflow\Util\Properties\Properties;

/**
 * Class AbstractWorkflowFactory
 *
 * @package OldTown\Workflow\Loader
 */
abstract class AbstractWorkflowFactory implements WorkflowFactoryInterface, WorkflowFactoryConfigInterface
{
    /**
     * @var PropertiesInterface
     */
    protected $properties;

    /**
     *
     */
    public function __construct()
    {
        $this->properties = new Properties();
    }

    /**
     * @return PropertiesInterface
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param PropertiesInterface $p
     */
    public function init(PropertiesInterface $p)
    {
        $this->properties = $p;
    }

    /**
     *
     */
    public function initDone()
    {

    }
}
