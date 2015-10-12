<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest;

/**
 * Test bootstrap, for setting up autoloading
 *
 * @subpackage UnitTest
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
     * Возвращает путь до директории с данными для тестов
     *
     * @return string
     */
    public static function getPathToDataDir()
    {
        if (static::$pathToDataDir) {
            return static::$pathToDataDir;
        }

        static::$pathToDataDir = __DIR__ . '/data';

        return static::$pathToDataDir;
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
