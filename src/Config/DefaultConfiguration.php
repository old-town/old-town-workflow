<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Config;

use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InvalidParsingWorkflowException;
use OldTown\Workflow\Loader\UrlWorkflowFactory;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Loader\WorkflowFactoryInterface;
use OldTown\Workflow\Loader\XmlUtil;
use OldTown\Workflow\Util\Properties\Properties;
use OldTown\Workflow\Util\VariableResolverInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\Exception\StoreException;
use Psr\Http\Message\UriInterface;
use OldTown\Workflow\Util\DefaultVariableResolver;
use DOMDocument;
use DOMElement;

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
     * @var WorkflowFactoryInterface
     */
    private $factory;

    /**
     * Хранилище состояния workflow
     *
     * @var WorkflowStoreInterface
     */
    private $store;

    /**
     *
     */
    public function __construct()
    {
        $this->variableResolver = new DefaultVariableResolver();
        $this->factory = new UrlWorkflowFactory();
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

            libxml_use_internal_errors(true);


            $xmlDoc = new DOMDocument();
            $resultLoadXml = $xmlDoc->loadXML($content);

            if (!$resultLoadXml) {
                $error = libxml_get_last_error();
                if ($error instanceof \LibXMLError) {
                    $errMsg = "Error in workflow xml.\n";
                    $errMsg .= "Message: {$error->message}.\n";
                    $errMsg .= "File: {$error->file}.\n";
                    $errMsg .= "Line: {$error->line}.\n";
                    $errMsg .= "Column: {$error->column}.";

                    throw new InvalidParsingWorkflowException($errMsg);
                }
            }
            /** @var DOMElement $root */
            $root = $xmlDoc->getElementsByTagName('osworkflow')->item(0);


            $p = XmlUtil::getChildElement($root, 'persistence');
            $resolver = XmlUtil::getChildElement($root, 'resolver');
            $factoryElement =  XmlUtil::getChildElement($root, 'factory');


            if (null !== $resolver && $resolver->hasAttribute('class')) {
                $resolverClass = XmlUtil::getRequiredAttributeValue($resolver, 'class');

                if (!class_exists($resolverClass)) {
                    $errMsg = "Для variableResolver указан не существующий класс {$resolverClass}";
                    throw new FactoryException($errMsg);
                }

                $variableResolver = new $resolverClass();
                if (!$variableResolver instanceof VariableResolverInterface) {
                    $errMsg = 'variableResolver должен реализовывать интерфейс VariableResolverInterface';
                    throw new FactoryException($errMsg);
                }
                $this->variableResolver = $variableResolver;
            }

            $this->persistenceClass = XmlUtil::getRequiredAttributeValue($p, 'class');

            $args = XmlUtil::getChildElements($p, 'property');

            foreach ($args as $arg) {
                $key = XmlUtil::getRequiredAttributeValue($arg, 'key');
                $value = XmlUtil::getRequiredAttributeValue($arg, 'value');
                $this->persistenceArgs[$key] = $value;
            }

            if (null !== $factoryElement) {
                $class = null;
                try {
                    $factoryClassName = XmlUtil::getRequiredAttributeValue($factoryElement, 'class');

                    if (!class_exists($factoryClassName)) {
                        $errMsg = "Для фабрики workflow указан несуществующий класс {$factoryClassName}";
                        throw new FactoryException($errMsg);
                    }
                    /** @var WorkflowFactoryInterface $factory */
                    $factory = new $factoryClassName();

                    if (!$factory instanceof WorkflowFactoryInterface) {
                        $errMsg = 'Фабрика должна реализовывать интерфейся WorkflowFactoryInterface';
                        throw new FactoryException($errMsg);
                    }

                    $properties = new Properties();
                    $props = XmlUtil::getChildElements($factoryElement, 'property');

                    foreach ($props as $e) {
                        $key = XmlUtil::getRequiredAttributeValue($e, 'key');
                        $value = XmlUtil::getRequiredAttributeValue($e, 'value');
                        $properties->setProperty($key, $value);
                    }

                    $factory->init($properties);
                    $factory->initDone();

                    $this->factory = $factory;
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
     *
     * @throws FactoryException
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
     * @return VariableResolverInterface|DefaultVariableResolver
     */
    public function getVariableResolver()
    {
        return $this->variableResolver;
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
        $this->getFactory()->removeWorkflow($workflow);
    }

    /**
     * Сохраняет Workflow
     * @param string $name имя сохраняемого workflow
     * @param WorkflowDescriptor $descriptor дескриптор workflow
     * @param boolean $replace - флаг определяющий, можно ли замениить workflow
     *
     * @return boolean
     *
     * @throws FactoryException
     * @throws \OldTown\Workflow\Exception\InvalidWorkflowDescriptorException
     *
     */
    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace = false)
    {
        $this->getFactory()->saveWorkflow($name, $descriptor, $replace);
    }

    /**
     * @return WorkflowFactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Возвращает имя класса описвающего хранилидище, в котором сохраняется workflow
     *
     * @return string
     */
    public function getPersistence()
    {
        return $this->persistenceClass;
    }

    /**
     * Получить аргументы хранилища
     *
     * @return array
     */
    public function getPersistenceArgs()
    {
        return $this->persistenceArgs;
    }


    /**
     * Возвращает имя дескриптора workflow
     *
     * @param string $name имя workflow
     * @throws FactoryException
     * @return WorkflowDescriptor
     */
    public function getWorkflow($name)
    {
        $workflow = $this->getFactory()->getWorkflow($name);

        if (!$workflow instanceof WorkflowDescriptor) {
            throw new FactoryException('Unknown workflow name');
        }

        return $workflow;
    }

    /**
     * Получает список имен всех доступных workflow
     * @throws FactoryException
     * @return String[]
     */
    public function getWorkflowNames()
    {
        $names = $this->getFactory()->getWorkflowNames();

        return $names;
    }

    /**
     * Получает хранилище Workflow
     *
     * @return WorkflowStoreInterface
     * @throws StoreException
     * @throws \OldTown\Workflow\Exception\FactoryException
     */
    public function getWorkflowStore()
    {
        if (!$this->store) {
            $class = $this->getPersistence();

            if (!class_exists($class)) {
                $errMsg = sprintf(
                    'Отсутствует класс хранилища %s',
                    $class
                );
                throw new FactoryException($errMsg);
            }


            $store = new $class();
            if (!$store instanceof WorkflowStoreInterface) {
                throw new FactoryException('Ошибка при создание хранилища');
            }

            $storeArgs = $this->getPersistenceArgs();
            $store->init($storeArgs);

            $this->store = $store;
        }

        return $this->store;
    }
}
