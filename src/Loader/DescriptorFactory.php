<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMNode;

/**
 * Class DescriptorFactory
 *
 * @package OldTown\Workflow\Loader
 */
class DescriptorFactory
{
    /**
     * @var DescriptorFactory
     */
    private static $factory;

    /**
     * @return DescriptorFactory
     */
    public static function getFactory()
    {
        if (!self::$factory instanceof self) {
            self::$factory = new DescriptorFactory();
        }
        return self::$factory;
    }

    /**
     * @param DescriptorFactory $factory
     */
    public static function setFactory(DescriptorFactory $factory)
    {
        self::$factory = $factory;
    }

    /**
     * @param DOMNode $root
     *
     * @return WorkflowDescriptor
     */
    public function createWorkflowDescriptor(DOMNode $root)
    {
        $workflowDescriptor = new WorkflowDescriptor($root);

        return $workflowDescriptor;
    }

}
