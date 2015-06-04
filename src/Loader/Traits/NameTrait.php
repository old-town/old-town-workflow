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
trait NameTrait
{
    /**
     * @var string
     */
   protected $name;


    /**
     * Возвращает имя
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Устанавливает имя
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Парсит элемент и извлекает из имя
     *
     * @param DOMElement $element
     * @param bool       $flagRequired
     */
    protected function parseName(DOMElement $element, $flagRequired = false)
    {
        if ($flagRequired || (!$flagRequired && $element->hasAttribute('name'))) {
            $this->name = XmlUtil::getRequiredAttributeValue($element, 'name');
        }
    }
}
