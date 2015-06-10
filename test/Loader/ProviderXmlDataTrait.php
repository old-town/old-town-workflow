<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\test\Loader;

/**
 * Class FunctionDescriptorTest
 *
 * @package OldTown\Workflow\Test\Loader
 */
trait ProviderXmlDataTrait
{
    /**
     * Путь до xml файлов используемых для тестирования
     *
     * @var string
     */
    protected $pathToXmlFile;

    /**
     * Возвращает узел для тестирования
     *
     * @param string $fileName
     * @param string $xpathPattern
     *
     * @return \DOMElement
     */
    public function getTestNode($fileName, $xpathPattern)
    {
        $pathToFile = $this->pathToXmlFile . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($pathToFile)) {
            $errMsg = "Отсутствует файл с тестовыми данными {$pathToFile}";
            throw new \RuntimeException($errMsg);
        }

        $xmlDoc = new \DOMDocument();
        $xmlDoc->load($pathToFile);

        $xpath = new \DOMXpath($xmlDoc);
        $elements = $xpath->query($xpathPattern);

        if (1 !== $elements->length) {
            $errMsg = "Некорректный списко элементов для xpath выражения {$xpathPattern}";
            throw new \RuntimeException($errMsg);
        }

        $element = $elements->item(0);

        return $element;
    }
}
