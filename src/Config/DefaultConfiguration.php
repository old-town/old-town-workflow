<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Config;

use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Util\VariableResolverInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\Exception\StoreException;
use Psr\Http\Message\UriInterface;
use SimpleXMLElement;
use OldTown\Workflow\Util\DefaultVariableResolver;

/**
 * Interface ConfigurationInterface
 *
 * @package OldTown\Workflow\Config
 */
class  DefaultConfiguration implements ConfigurationInterface
{
    /**
     * @var DefaultConfiguration
     */
    protected static $instance;

    /**
     * Пути по умолчнаию до файла с конфигом
     *
     * @var array
     */
    protected static $defaultPathsToConfig = [];

    /**
     * Имя файла конфига по умолчанию
     *
     * @var string
     */
    protected static $configFileName = 'osworkflow.xml';

    /**
     * @var array
     */
    private $cache = [];

    /**
     * Флаг определяющий было ли иницилизированно workflow
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * @var VariableResolverInterface
     */
    private $variableResolver;

    /**
     * Имя класса хранилища состояния workflow
     *
     * @var string
     */
    private $persistenceClass;

    /**
     * Настройки хранилища
     *
     * @var array
     */
    private $persistenceArgs = [];

    /**
     *
     */
    public function __construct()
    {
        $this->variableResolver = new DefaultVariableResolver();
        $this->initDefaultPathsToConfig();
    }

    /**
     * Иницализация путей по которым происходит поиск
     *
     * @return void
     */
    protected function initDefaultPathsToConfig()
    {
        static::$defaultPathsToConfig[] = __DIR__ . '/../../config';
    }

    /**
     * @param string $workflowName
     * @param object $layout
     * @return void
     */
    public function setLayout($workflowName, $layout)
    {

    }

    /**
     * @param string $workflowName
     * @return Object
     */
    public function getLayout($workflowName)
    {
        return null;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function isModifiable($name)
    {
        return false;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * Возможность статически получать экземпляр конфигурации по умолчанию для workflow
     *
     * @return DefaultConfiguration
     */
    public static function getInstance()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::$instance;
    }

    /**
     * Возвращает true, если фабрика инициализировала объект конфигурации
     *
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Загружает указанный файл конфигурации
     *
     * @param UriInterface|null $url
     * @return void
     * @throws FactoryException
     */
    public function load(UriInterface $url = null)
    {
        try {

            $content = $this->getContentConfigFile($url);

            $xml = new SimpleXMLElement($content);

            $rootElements = $xml->xpath('//osworkflow');
            if (1 !== count($rootElements)) {
                $errMsg = 'В конфиге не найден элемент osworkflow';
                throw new FactoryException($errMsg);
            }
            $root = (array)$rootElements[0];
            /** @var SimpleXMLElement $p */
            $p = array_key_exists('persistence', $root) ? $root['persistence'] : null;
            /** @var SimpleXMLElement $resolver */
            $resolver = array_key_exists('resolver', $root) ? $root['resolver'] : null;
            /** @var SimpleXMLElement $factoryElement */
            $factoryElement = array_key_exists('factory', $root) ? $root['factory'] : null;


            if (null !== $resolver) {
                $resolverElementAttributes = is_object($resolver) ? $resolver->attributes() : [];
                if (isset($resolverElementAttributes['class'])) {
                    /** @var SimpleXMLElement $resolverElementAttribute */
                    $resolverElementAttribute = $resolverElementAttributes['class'];
                    $variableResolverClassName = (string)$resolverElementAttribute;

                    $variableResolver = new $variableResolverClassName();
                    if (!$variableResolver instanceof VariableResolverInterface) {
                        $errMsg = 'variableResolver должен реализовывать интерфейс VariableResolverInterface';
                        throw new FactoryException($errMsg);
                    }
                    $this->variableResolver = $variableResolver;
                }
            }

            if (!is_object($p)) {
                $errMsg = 'В конфигурационном файле остутствует корректный блок persistence';
                throw new FactoryException($errMsg);
            }
            $persistenceElementAttributes = $p->attributes();

            if (!isset($persistenceElementAttributes['class'])) {
                $errMsg = 'У тега persistence отсутствует атрибут class';
                throw new FactoryException($errMsg);
            }
            $this->persistenceClass = (string)$persistenceElementAttributes['class'];

            $args = $p->xpath('property');

            foreach ($args as $arg) {
                $argAttribute = $arg->attributes();

                if (isset($argAttribute['key']) && $argAttribute['value']) {
                    $key = (string)$argAttribute['key'];
                    $value = (string)$argAttribute['value'];
                    $this->persistenceArgs[$key] = $value;
                }
            }

            if (null !== $factoryElement) {
                $class = null;
                try {
                    $factoryElementAttributes = $factoryElement->attributes();
                    if (!isset($factoryElementAttributes['class'])) {
                        $errMsg = 'Не указан класс фабрики';
                        throw new FactoryException($errMsg);
                    }

                    $factoryClassName = $factoryElementAttributes['class'];
                    $factory = new $factoryClassName();



                } catch (FactoryException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    $class = (string)$class;
                    $errMsg = "Ошибка создания фабрики workflow для класса {$class}";
                    throw new FactoryException($errMsg, $e->getCode(), $e);
                }

                $this->initialized = true;
            }
        } catch (FactoryException $e) {
            throw $e;
        } catch (\Exception $e) {
            $errMsg = 'Ошибка при работе с конфигом workflow';
            throw new FactoryException($errMsg, $e->getCode(), $e);
        }

    }


    /**
     * @param UriInterface $url
     *
     * @return string
     */
    protected function getContentConfigFile(UriInterface $url = null)
    {

        if (null !== $url) {
            $urlStr = (string)$url;
            $content = file_get_contents($urlStr);

            return $content;
        }

        $paths = static::getDefaultPathsToConfig();

        $content = null;
        foreach ($paths as $path) {
            $path = realpath($path);
            if ($path) {
                $filePath = $path . DIRECTORY_SEPARATOR . static::getConfigFileName();
                if (file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                    break;
                }
            }
        }

        if (null === $content) {
            $errMsg = 'Не удалось прочитать конфигурационный файл';
            throw new FactoryException($errMsg);
        }

        return $content;
    }

    /**
     * @return array
     */
    public static function getDefaultPathsToConfig()
    {
        return self::$defaultPathsToConfig;
    }

    /**
     * @param array $defaultPathsToConfig
     */
    public static function setDefaultPathsToConfig(array $defaultPathsToConfig = [])
    {
        self::$defaultPathsToConfig = $defaultPathsToConfig;
    }

    /**
     * @param string $path
     */
    public static function addDefaultPathToConfig($path)
    {
        $path = (string)$path;

        array_unshift(self::$defaultPathsToConfig, $path);
    }


    /**
     * @return string
     */
    public static function getConfigFileName()
    {
        return self::$configFileName;
    }

    /**
     * @param string $configFileName
     */
    public static function setConfigFileName($configFileName)
    {
        self::$configFileName = $configFileName;
    }


    /**
     * Возвращает resolver для работы с переменными
     *
     * @return VariableResolverInterface
     */
    public function  getVariableResolver()
    {
        return $this->variableResolver;
    }

########################################################################################################################
#Методы заглушки, при портирование заменять на реализацию ##############################################################
########################################################################################################################




    /**
     * Возвращает имя класса описвающего хранилидище, в котором сохраняется workflow
     *
     * @return string
     */
    public function getPersistence()
    {

    }

    /**
     * Получить аргументы хранилища
     *
     * @return array
     */
    public function getPersistenceArgs()
    {

    }


    /**
     * Возвращает имя дескриптора workflow
     *
     * @param string $name имя workflow
     * @throws FactoryException
     * @return WorkflowDescriptor
     */
    public function  getWorkflow($name)
    {

    }

    /**
     * Получает список имен всех доступных workflow
     * @throws FactoryException
     * @return String[]
     */
    public function  getWorkflowNames()
    {

    }

    /**
     * Получает хранилище Workflow
     *
     * @return WorkflowStoreInterface
     * @throws StoreException
     */
    public function getWorkflowStore()
    {

    }

    /**
     * Удаляет workflow
     *
     * @param string $workflow имя удаляемого workflow
     * @return boolean в случае успешного удаления возвращает true, в противном случае false
     * @throws FactoryException
     */
    public function removeWorkflow($workflow)
    {

    }

    /**
     * Сохраняет Workflow
     * @param string $name имя сохраняемого workflow
     * @param WorkflowDescriptor $descriptor дескриптор workflow
     * @param boolean $replace - флаг определяющий, можно ли замениить workflow
     * @throws FactoryException
     * @return boolean
     */
    public function  saveWorkflow($name, WorkflowDescriptor $descriptor, $replace = false)
    {

    }
}
