<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use OldTown\Workflow\Loader\Traits\ArgsInterface;
use OldTown\Workflow\Loader\WriteXmlInterface;

/**
 * Class FunctionDescriptorTest
 *
 * @package OldTown\Workflow\Test\Loader
 */
trait ArgumentsTraitTest
{
    /**
     * Возвращает класс тестируемого декскриптора
     *
     * @return string
     */
    abstract public function getDescriptorClassName();

    /**
     * Кеш данных для тестирования записи атрибутов
     *
     * @var array
     */
    protected $writeXmlArgTestData;

    /**
     * Данные для стандартного тестирования записи аргументов
     *
     * @var array
     */
    protected $defaultWriteXmlArgTestData = [
        [
            'diDescriptor'  => null,
            'argName'       => 'testArgName',
            'argValue'      => 'testArgValue',
            'nodeType'      => XML_TEXT_NODE,
            'expectedValue' => 'testArgValue',
        ]
    ];

    /**
     * Возвращает данные для тестирования записи аргументов
     *
     * @return array
     */
    public function writeXmlArgTestData()
    {
        if (null !== $this->writeXmlArgTestData) {
            return $this->writeXmlArgTestData;
        }

        $r = new \ReflectionObject($this);

        $customMethod = $r->hasMethod('writeXmlArgCustomTestData') ? $r->getMethod('writeXmlArgCustomTestData') : null;
        $valueCustomMethod = null;
        if ($customMethod instanceof \ReflectionMethod) {
            $valueCustomMethod = $customMethod->invoke($this);
        }

        $customData = is_array($valueCustomMethod) ? $valueCustomMethod : [];

        $data = array_merge($this->defaultWriteXmlArgTestData, $customData);

        $this->writeXmlArgTestData = $data;

        return $data;
    }

    /**
     * Тестирование сохранения аргументов
     *
     * @dataProvider writeXmlArgTestData
     *
     * @param callable $diDescriptor
     * @param string   $argName
     * @param string   $argValue
     * @param int      $nodeType
     * @param string   $expectedValue
     */
    public function testWriteArg(callable $diDescriptor = null, $argName, $argValue, $nodeType = XML_TEXT_NODE, $expectedValue = null)
    {
        if (null === $expectedValue) {
            $expectedValue = $argValue;
        }
        $descriptorClassName = $this->getDescriptorClassName();
        $r = new \ReflectionClass($descriptorClassName);

        /** @var WriteXmlInterface $descriptor */
        $descriptor = $r->newInstance();

        if (!$descriptor instanceof WriteXmlInterface) {
            $errMsg = 'Дескриптор должен реализовывать WriteXmlInterface';
            throw new \RuntimeException($errMsg);
        }

        if (!$descriptor instanceof ArgsInterface) {
            $errMsg = sprintf('Дескриптор %s не поддерживает рабуот с аргументами', get_class($descriptor));
            throw new \RuntimeException($errMsg);
        }

        if (null !== $diDescriptor) {
            call_user_func($diDescriptor, $descriptor);
        }

        /** @var  ArgsInterface $descriptor */
        $descriptor->setArg($argName, $argValue);

        $rO = new \ReflectionObject($this);
        $defaultDiDescriptor = $rO->hasMethod('defaultDiDescriptor') ? $rO->getMethod('defaultDiDescriptor') : null;
        if ($defaultDiDescriptor instanceof \ReflectionMethod) {
            $defaultDiDescriptor->invoke($this, $descriptor);
        }


        $domDescriptor = new \DOMDocument();
        /** @var  WriteXmlInterface $descriptor */
        $domNode = $descriptor->writeXml($domDescriptor);

        $generatedXml = $domDescriptor->saveXML($domNode);


        $testedDomDescriptor = new \DOMDocument();
        $testedDomDescriptor->loadXML($generatedXml);

        $xpath = new \DOMXpath($testedDomDescriptor);
        $elementDescriptor = $testedDomDescriptor->firstChild;

        $xpathPattern = "./arg[@name='{$argName}']";
        $argElements = $xpath->query($xpathPattern, $elementDescriptor);

        $errMsg = sprintf('Не найден дескриптор соответствующий xpath %s', $xpathPattern);
        call_user_func([static::class, 'assertEquals'], $argElements->length, 1, $errMsg);

        $argElement = $argElements->item(0);

        $errMsg = sprintf('Некорректный тип значения аргумента: ожидаемый тип  %s, фактический',
            $nodeType,
            $argElement->firstChild->nodeType
        );
        call_user_func([static::class, 'assertEquals'], $argElement->firstChild->nodeType, $nodeType, $errMsg);

        $errMsg = sprintf('Несовпадает значение аргумента: ожидается %s, в действительности %s',
            $expectedValue,
            $argElement->firstChild->nodeValue
        );
        call_user_func([static::class, 'assertEquals'], $argElement->firstChild->nodeValue, $expectedValue, $errMsg);
    }
}
