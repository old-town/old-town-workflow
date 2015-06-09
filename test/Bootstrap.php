<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test;

use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Test bootstrap, for setting up autoloading
 *
 * @subpackage UnitTest
 */
class Bootstrap
{
    /**
     * Настройка тестов
     */
    public static function init()
    {
        static::initAutoloader();
    }


    /**
     * Инициализация автозагрузчика
     *
     * @return void
     */
    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        $loader = null;
        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        }

        if (!class_exists(AutoloaderFactory::class)) {
            $zf2Path = getenv('ZF2_PATH') ?: (defined('ZF2_PATH') ? constant('ZF2_PATH') : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            if (null !== $loader) {
                $loader->add('Zend', $zf2Path . '/Zend');
            } else {
                include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
            }
        }
        AutoloaderFactory::factory(array(
            StandardAutoloader::class => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    'OldTown\Workflow' => __DIR__ . '/../src/',
                    __NAMESPACE__ => __DIR__
                )
            )
        ));
        AutoloaderFactory::factory(array(
            StandardAutoloader::class => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    'OldTown\Workflow\Test' => __DIR__ . '/../test/',
                    __NAMESPACE__ => __DIR__
                )
            )
        ));


    }

    /**
     * @param $path
     *
     * @return bool|string
     */
    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}

Bootstrap::init();
