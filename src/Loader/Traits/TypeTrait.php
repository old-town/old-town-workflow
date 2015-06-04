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
trait TypeTrait
{
    /**
     * @var string
     */
   protected $type;


    /**
     * Возвращает имя
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Устанавливает имя
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Парсит элемент и извлекает из имя
     *
     * @param DOMElement $element
     * @param bool       $flagRequired
     */
    protected function parseType(DOMElement $element, $flagRequired = true)
    {
        if ($flagRequired || (!$flagRequired && $element->hasAttribute('type'))) {
            $this->type = XmlUtil::getRequiredAttributeValue($element, 'type');
        }
    }
}
