<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use OldTown\Workflow\Loader\WriteXmlInterface;
use DOMDocument;

/**
 * Class FunctionDescriptorTest
 *
 * @package OldTown\Workflow\Test\Loader
 */
trait TestAttributeTrait
{
    /**
     * Возвращает узел для тестирования
     *
     * @param string $fileName
     * @param string $xpathPattern
     *
     * @return \DOMNode
     */
    abstract public function getTestNode($fileName, $xpathPattern);

    /**
     * Тестируем атрибуты элемента
     *
     * @dataProvider testAttributesData
     *
     * @param string $class
     * @param string $fileName
     * @param string $xpathPattern
     * @param array  $attributes
     */
    public function helperTestAttributeFunctionDescriptor($class, $fileName, $xpathPattern, array $attributes = [])
    {

        /** @var \DOMElement $testNode */
        $testNode = $this->getTestNode($fileName, $xpathPattern);
        $descriptor = new $class($testNode);

        foreach ($attributes as $attribute) {
            $descriptorMethod = $attribute['descriptorMethod'];
            $valueAttributeDescriptor = call_user_func([$descriptor, $descriptorMethod]);

            if (!$attribute['required'] && !$testNode->hasAttribute($attribute['xmlAttributeName'])) {
                $msg = sprintf('Метод %s класса %s должен возвращать null, если у элемента %s, не указан атрибут %s',
                    $descriptorMethod,
                    get_class($descriptor),
                    $testNode->nodeName,
                    $attribute['xmlAttributeName']
                );
                call_user_func([static::class, 'assertNull'], $valueAttributeDescriptor, $msg);
            } else {
                $valueXmlAttribute = $testNode->getAttribute($attribute['xmlAttributeName']);

                $msg = sprintf('Метод %s класса %s должен возвращать %s: актуальное значение %s',
                    $descriptorMethod,
                    get_class($descriptor),
                    $valueXmlAttribute,
                    $valueAttributeDescriptor
                );

                call_user_func([static::class, 'assertEquals'], $valueXmlAttribute, $valueAttributeDescriptor, $msg);
            }
        }
    }



    /**
     * Метод для автоматизации тестирования значений атрибутов
     *
     * @param string $class         - имя класса дескриптора элемента workflow
     * @param string $setter        - имя метода устанавливающего  значение атрибута из дескриптора
     * @param string $getter        - имя метода возвращающего  значение атрибута из дескриптора
     * @param string $xpathElement  - xpath выражение позволяющие получить тестируемый элемент в сгенерированном xml
     * @param        $attributeName - имя атрибута в сгенерированном xml
     * @param        $value         - значение атрибута
     */
    protected function saveAttributeTest($class, $setter, $getter, $xpathElement, $attributeName, $value)
    {
        /** @var WriteXmlInterface $descriptor */


        $r = new \ReflectionClass($class);
        $descriptor = $r->newInstance();
        if ($descriptor instanceof WriteXmlInterface) {
            $errMsg = 'Объект должен реализовывать интерфейс WriteXmlInterface';
            throw new \RuntimeException($errMsg);
        }

        call_user_func([$descriptor, $setter], $value);

        $domDescriptor = new DOMDocument();
        $domNode = $descriptor->writeXml($domDescriptor);

        $strXml = $domDescriptor->saveXML($domNode);

        $createdXml = new DOMDocument();
        $createdXml->loadXML($strXml);

        $xpath = new \DOMXpath($createdXml);
        $elements = $xpath->query($xpathElement);


        if (1 !== $elements->length) {
            $errMsg = "Некорректный списко элементов для xpath выражения {$xpathElement}";
            throw new \RuntimeException($errMsg);
        }

        /** @var \DOMElement $element */
        $element = $elements->item(0);

        $hasAttribute = $element->hasAttribute($attributeName);

        $msg = sprintf('У элемента %s не сгенерился атрибут %s', $element->nodeName, $attributeName);
        call_user_func([static::class, 'assertTrue'], $hasAttribute, $msg);


        $expectedValue = call_user_func([$descriptor, $getter]);

        $actualValue = $element->getAttribute($attributeName);

        $msg = sprintf('Ожидаемое значение атрибута %s элемента %s должно быть %s: актуальное значение %s',
            $attributeName,
            $element->nodeName,
            $expectedValue,
            $actualValue
        );

        call_user_func([static::class, 'assertEquals'], $expectedValue, $actualValue, $msg);
    }
}
