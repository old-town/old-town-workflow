<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use OldTown\Workflow\Loader\Traits\ArgsInterface;
use OldTown\Workflow\Loader\WriteXmlInterface;
use OldTown\Workflow\Loader\XmlUtil;

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
     * Возвращает узел для тестирования
     *
     * @param string $fileName
     * @param string $xpathPattern
     *
     * @return \DOMElement
     */
    abstract public function getTestNode($fileName, $xpathPattern);

    /**
     * Возвращает узлы для тестирования
     *
     * @param string $fileName
     * @param string $xpathPattern
     *
     * @return \DOMElement
     */
    abstract public function getTestNodes($fileName, $xpathPattern);

    /**
     * Кеш данных для тестирования записи атрибутов
     *
     * @var array
     */
    protected $writeXmlArgTestData;

    /**
     * Кеш данных для тестирования чтения аргументов
     *
     * @var array
     */
    protected $readXmlArgTestData;

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
     * Данные для стандартного тестирования чтения аргументов
     *
     * @var array
     */
    protected $defaultReadXmlArgTestData = [

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


    /**
     * Возвращает данные для тестирования чтения аргументов
     *
     * @return array
     */
    public function readXmlArgTestData()
    {
        if (null !== $this->readXmlArgTestData) {
            return $this->readXmlArgTestData;
        }

        $r = new \ReflectionObject($this);

        $customMethod = $r->hasMethod('readXmlArgCustomTestData') ? $r->getMethod('readXmlArgCustomTestData') : null;
        $valueCustomMethod = null;
        if ($customMethod instanceof \ReflectionMethod) {
            $valueCustomMethod = $customMethod->invoke($this);
        }

        $customData = is_array($valueCustomMethod) ? $valueCustomMethod : [];

        $data = array_merge($this->defaultReadXmlArgTestData, $customData);

        $this->readXmlArgTestData = $data;

        return $data;
    }

    /**
     * Тестирование чтения аргументов
     *
     * @dataProvider readXmlArgTestData
     * @param string $file
     * @param string $xpathRoot
     */
    public function testReadArg($file, $xpathRoot)
    {
        /** @var \DOMElement $testNode */
        $testNode = $this->getTestNode($file, $xpathRoot);

        $descriptorClassName = $this->getDescriptorClassName();
        $r = new \ReflectionClass($descriptorClassName);

        /** @var ArgsInterface $descriptor */
        $descriptor = $r->newInstance($testNode);

        $xpath = new \DOMXpath($testNode->ownerDocument);
        $argsDomElements = $xpath->query('arg', $testNode);

        $expectedArgs = [];
        for ($i = 0; $i < $argsDomElements->length; $i++) {
            /** @var \DOMElement $element */
            $element = $argsDomElements->item($i);
            $name = XmlUtil::getRequiredAttributeValue($element, 'name');
            $value = XmlUtil::getText($element);

            $expectedArgs[$name] = $value;
        }

        foreach ($expectedArgs as $argName => $argValue) {
            $errMsg = sprintf('Неверное значение для аргумента с именем %s', $argName);
            call_user_func([static::class, 'assertEquals'], $descriptor->getArg($argName), $argValue, $errMsg);
        }

        $errMsg = 'Разное колличество аргументов';
        call_user_func([static::class, 'assertEquals'], count($descriptor->getArgs()), count($expectedArgs), $errMsg);

    }
}
