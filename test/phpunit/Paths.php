<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnit\Test;

/**
 * Class Paths
 *
 * @package OldTown\Workflow\PhpUnit\Test
 */
class Paths
{
    /**
     * Путь до директории с данными для тестов
     *
     * @var string|null
     */
    protected static $pathToDataDir;

    /**
     * Путь до временной директории, куда могу записывать свои файлы тесты
     *
     * @var string|null
     */
    protected static $pathToTestDataDir;

    /**
     * Ресурсы используемые разными тестами
     *
     * @var string|null
     */
    protected static $pathToCommonDataDir;

    /**
     * Корректный конфиг workflow+некорректный файл самого workflow
     *
     * @var string
     */
    protected static $pathToInvalidWorkflowDir;

    /**
     * Каталог с примерами некорректных файлов конфига workflow
     *
     * @var string
     */
    protected static $pathToInvalidWorkflowConfig;

    /**
     * Директория содержит файлы используемые для тестирования сохранения workflow
     *
     * @var string
     */
    protected static $pathToSaveWorkflowDir;

    /**
     * Путь до каталого содержащего тестовые данные для тестирования наборы утилитарного функционала для разбора xml
     *
     * @var string
     */
    protected static $xmlUtilTest;

    /**
     * Возвращает путь до каталого содержащего тестовые данные для тестирования наборы утилитарного функционала для разбора xml
     *
     * @return string
     */
    public static function getXmlUtilTest()
    {
        if (static::$xmlUtilTest) {
            return static::$xmlUtilTest;
        }

        static::$xmlUtilTest = __DIR__ . '/_files/xml-util';

        return static::$xmlUtilTest;
    }

    /**
     * Возвращает путь до директории с данными для тестов
     *
     * @return string
     */
    public static function getPathToSaveWorkflowDir()
    {
        if (static::$pathToSaveWorkflowDir) {
            return static::$pathToSaveWorkflowDir;
        }

        static::$pathToSaveWorkflowDir = __DIR__ . '/_files/save-workflow';

        return static::$pathToSaveWorkflowDir;
    }

    /**
     * Возвращает путь до директории с данными для тестов
     *
     * @return string
     */
    public static function getPathToDataDir()
    {
        if (static::$pathToDataDir) {
            return static::$pathToDataDir;
        }

        static::$pathToDataDir = __DIR__ . '/_files';

        return static::$pathToDataDir;
    }

    /**
     * Возвращает путь до директории с данными для тестов
     *
     * @return string
     */
    public static function getPathToCommonDataDir()
    {
        if (static::$pathToCommonDataDir) {
            return static::$pathToCommonDataDir;
        }

        static::$pathToCommonDataDir = __DIR__ . '/_files/common';

        return static::$pathToCommonDataDir;
    }


    /**
     * Возвращает путь до каталога с примерами некорректных файлов конфига workflow
     *
     * @return string
     */
    public static function getPathToInvalidWorkflowConfig()
    {
        if (static::$pathToInvalidWorkflowConfig) {
            return static::$pathToInvalidWorkflowConfig;
        }

        static::$pathToInvalidWorkflowConfig = __DIR__ . '/_files/invalid-workflow-config';

        return static::$pathToInvalidWorkflowConfig;
    }

    /**
     * Возвращает путь до директории содержащий корректный конфиг workflow+некорректный файл самого workflow
     *
     * @return string
     */
    public static function getPathToInvalidWorkflowDir()
    {
        if (static::$pathToInvalidWorkflowDir) {
            return static::$pathToInvalidWorkflowDir;
        }

        static::$pathToInvalidWorkflowDir = __DIR__ . '/_files/invalid-workflow';

        return static::$pathToInvalidWorkflowDir;
    }

    /**
     * Возвращает путь до директории с данными для тестов
     *
     * @return string
     */
    public static function getPathToTestDataDir()
    {
        if (static::$pathToTestDataDir) {
            return static::$pathToTestDataDir;
        }

        static::$pathToTestDataDir = __DIR__ . '/../../data/test';

        return static::$pathToTestDataDir;
    }
}
