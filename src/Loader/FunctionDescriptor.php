<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Loader\Traits;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class FunctionDescriptor extends AbstractDescriptor
    implements Traits\ArgsInterface,
               Traits\TypeInterface,
               Traits\NameInterface
{
    use Traits\ArgsTrait, Traits\TypeTrait, Traits\IdTrait, Traits\NameTrait;

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
     * @param DOMElement $element
     *
     * @return void
     */
    protected function init(DOMElement $element)
    {
        $this->parseType($element);
        $this->parseId($element, false);
        $this->parseName($element, false);

        $this->parseArgs($element);
    }

}
