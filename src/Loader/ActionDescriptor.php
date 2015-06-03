<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class ActionDescriptor extends AbstractDescriptor
{

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {

        if (null !== $element) {
            $this->init($element);
        }

        parent::__construct($element);
    }

    /**
     * @param DOMElement $condition
     *
     * @return void
     */
    protected function init(DOMElement $condition)
    {

    }


}
