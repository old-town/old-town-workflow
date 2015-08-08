<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use DOMDocument;
use OldTown\Workflow\Exception\InvalidDescriptorException;
use OldTown\Workflow\Exception\InvalidWriteWorkflowException;


/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
class PermissionDescriptor extends AbstractDescriptor implements Traits\NameInterface, WriteXmlInterface
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
        if ($restrictTo instanceof DOMElement) {
            $this->restriction = new RestrictionDescriptor($restrictTo);
        }
    }

    /**
     * @return RestrictionDescriptor
     */
    public function getRestriction()
    {
        return $this->restriction;
    }

    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     *
     * @return DOMElement|null
     * @throws InvalidDescriptorException
     * @throws InvalidWriteWorkflowException
     */
    public function writeXml(DOMDocument $dom = null)
    {
        if (null === $dom) {
            $errMsg = 'Не передан DOMDocument';
            throw new InvalidWriteWorkflowException($errMsg);
        }
        $descriptor = $dom->createElement('permission');

        if ($this->hasId()) {
            $id = $this->getId();
            $descriptor->setAttribute('id', $id);
        }
        $name = $this->getName();
        if (null === $name) {
            $errMsg = 'Некорректное значение для атрибута name';
            throw new InvalidDescriptorException($errMsg);
        }
        $descriptor->setAttribute('name', $name);


        $restriction = $this->getRestriction();
        if (null === $restriction) {
            $errMsg = 'Некорректное значение для restriction';
            throw new InvalidDescriptorException($errMsg);
        }
        $restrictionElement = $restriction->writeXml($dom);
        if (null === $restrictionElement) {
            $errMsg = 'Некорректное значение сгенерированного xml для restriction';
            throw new InvalidDescriptorException($errMsg);
        }
        $descriptor->appendChild($restrictionElement);

        return $descriptor;
    }
}
