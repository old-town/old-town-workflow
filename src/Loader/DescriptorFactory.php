<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;

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
     * @param DOMElement $root
     *
     * @return WorkflowDescriptor
     */
    public function createWorkflowDescriptor(DOMElement $root)
    {
        $descriptor = new WorkflowDescriptor($root);

        return $descriptor;
    }


    /**
     * @param DOMElement $register
     *
     * @return RegisterDescriptor
     */
    public function createRegisterDescriptor(DOMElement $register = null)
    {
        $descriptor = new RegisterDescriptor($register);

        return $descriptor;
    }

    /**
     * @param DOMElement $element
     *
     * @return ConditionsDescriptor
     */
    public function  createConditionsDescriptor(DOMElement $element = null)
    {
        return new ConditionsDescriptor($element);
    }

    /**
     * @param DOMElement $element
     *
     * @return ConditionsDescriptor
     */
    public function  createConditionDescriptor(DOMElement $element = null)
    {
        return new ConditionDescriptor($element);
    }

    /**
     * @param DOMElement $element
     *
     * @return ConditionsDescriptor
     */
    public function createActionDescriptor(DOMElement $element = null)
    {
        return new ConditionDescriptor($element);
    }

}
