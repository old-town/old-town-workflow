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
     * @param Properties $p
     */
    public function __construct(Properties $p = null)
    {
        if (null === $p) {
            $p = new Properties();
        }
        $this->properties = $p;
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
