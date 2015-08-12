<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMElement;
use OldTown\Workflow\Exception\NotExistsRequiredAttributeException;

/**
 * Interface WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader
 */
abstract class XmlUtil
{
    /**
     * Ищет среди потомков элемента $parent, первый элемент с именем тега $childName
     *
     * @param DOMElement $parent
     * @param string $childName
     *
     * @return DOMElement|null
     */
    public static function getChildElement(DOMElement $parent, $childName)
    {
        $list = $parent->getElementsByTagName($childName);
        if ($list->length > 0) {
            return $list->item(0);
        }

        return null;
    }

    /**
     * Ищет среди потомков элемента $parent,  элементы с именем тега $childName
     *
     * @param DOMElement $parent
     * @param string $childName
     *
     * @return DOMElement[]|null
     */
    public static function getChildElements(DOMElement $parent, $childName)
    {
        $listElements = $parent->getElementsByTagName($childName);
        if ($listElements->length > 0) {
            $list = [];

            for ($i = 0; $i < $listElements->length; $i++) {
                $currentItem = $listElements->item($i);

                if (!$parent->isSameNode($currentItem->parentNode)) {
                    continue;
                }

                $list[] = $currentItem;
            }

            return $list;
        }

        return [];
    }

    /**
     * Ищет среди потомков элемента $parent, первый элемент с именем тега $childName и возвращает его текст
     *
     * @param DOMElement $parent
     * @param string $childName
     *
     * @return null
     */
    public static function getChildText(DOMElement $parent, $childName)
    {
        $child = self::getChildElement($parent, $childName);

        if (null === $child) {
            return null;
        }

        return self::getText($child);
    }

    /**
     * Получает текст из нод потомков
     *
     * @param DOMElement $node
     * @return string
     */
    public static function getText(DOMElement $node)
    {
        $s = '';
        for ($i = 0; $i < $node->childNodes->length; $i++) {
            $child = $node->childNodes->item($i);

            switch ($child->nodeType) {
                case XML_CDATA_SECTION_NODE:
                case XML_TEXT_NODE: {
                    $s .= $child->nodeValue;
                }
            }
        }

        return $s;
    }

    /**
     * @param DOMElement $node
     * @param string     $attributeName
     *
     * @return string
     */
    public static function getRequiredAttributeValue(DOMElement $node, $attributeName)
    {
        $attributeName = (string)$attributeName;

        $attribute = $node->attributes->getNamedItem($attributeName);
        if (!$attribute) {
            $errMsg = "Отсутствует атрибут {$attributeName} у тега {$node->nodeName}";
            $exception =  new NotExistsRequiredAttributeException($errMsg);
            $exception->setRequiredAttributeName($attributeName);

            throw $exception;
        }

        $value = $attribute->nodeValue;

        return $value;
    }


    /**
     * @fixme Реализовать методу \OldTown\Workflow\Loader\XmlUtil::encode
     *
     * @param string $string
     * @return string
     */
    public static function encode($string)
    {
        return $string;
    }
}
