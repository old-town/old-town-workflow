<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader\Traits;

use DOMElement;
use OldTown\Workflow\Loader\XmlUtil;

/**
 * Class ConditionDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
trait IdTrait
{
    /**
     * @param int $id
     *
     * @return $this
     */
    abstract public function setId($id);

    /**
     * Парсит элемент и извлекает из имя
     *
     * @param DOMElement $element
     * @param bool       $flagRequired
     */
    protected function parseId(DOMElement $element, $flagRequired = true)
    {
        if ($flagRequired || (!$flagRequired && $element->hasAttribute('id'))) {
            $id = XmlUtil::getRequiredAttributeValue($element, 'id');

            $this->setId($id);
        }
    }
}
