<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class PermissionDescriptor extends AbstractDescriptor implements Traits\NameInterface
{
    use Traits\NameTrait, Traits\IdTrait;

    /**
     * @var RestrictionDescriptor
     */
    protected $restriction;

    /**
     * @param $element
     */
    public function __construct(DOMElement $element = null)
    {
        parent::__construct($element);

        if (null !== $element) {
            $this->init($element);
        }
    }

    /**
     * @param DOMElement $permission
     *
     * @return void
     */
    protected function init(DOMElement $permission)
    {
        $this->parseName($permission);
        $this->parseId($permission, false);


        $restrictTo = XMLUtil::getChildElement($permission, 'restrict-to');
        $this->restriction = new RestrictionDescriptor($restrictTo);
    }

    /**
     * @return RestrictionDescriptor
     */
    public function getRestriction()
    {
        return $this->restriction;
    }

    /**
     * @param RestrictionDescriptor $restriction
     *
     * @return $this
     */
    public function setRestriction(RestrictionDescriptor $restriction)
    {
        $this->restriction = $restriction;

        return $this;
    }
}
