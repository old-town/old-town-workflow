<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Config;

use OldTown\Workflow\Loader\UrlWorkflowFactory;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\Util\DefaultVariableResolver;
use OldTown\Workflow\Util\VariableResolverInterface;
use Psr\Http\Message\UriInterface;
use OldTown\Workflow\Loader\WorkflowFactoryInterface;


/**
 * Interface ConfigurationInterface
 *
 * @package OldTown\Workflow\Config
 */
class  ArrayConfiguration implements ConfigurationInterface
{
    /**
     * @var string
     */
    const PERSISTENCE = 'persistence';

    /**
     * @var string
     */
    const PERSISTENCE_ARGS = 'persistenceArgs';

    /**
     * @var string
     */
    const VARIABLE_RESOLVER = 'variableResolver';

    /**
     * @var string
     */
    const WORKFLOW_FACTORY = 'factory';

    /**
     * Флаг определяющий было ли иницилизированно workflow
     *
     * @var bool
     */
    protected $initialized = true;

    /**
     * Фабрика для создания workflow
     *
     * @var WorkflowFactoryInterface
     */
    protected $factory;

    /**
     * Хранилище состояния workflow
     *
     * @var WorkflowStoreInterface
     */
    protected $workflowStore;

    /**
     * Имя класса хранилища состяония workflow
     *
     * @var string
     */
    protected $persistence;

    /**
     * Опции для настройки хранилища стостояния workflow
     *
     * @var array
     */
    protected $persistenceArgs = [];

    /**
     * @var VariableResolverInterface
     */
    protected $variableResolver;

    /**
     * @param $options
     *
     * @throws \OldTown\Workflow\Exception\FactoryException
     * @throws \OldTown\Workflow\Config\Exception\InvalidVariableResolverException
     * @throws \OldTown\Workflow\Config\Exception\InvalidWorkflowFactoryException
     */
    public function __construct(array $options = [])
    {
        $this->init($options);
    }

    /**
     * @param array $options
     *
     * @throws \OldTown\Workflow\Exception\FactoryException
     * @throws \OldTown\Workflow\Config\Exception\InvalidVariableResolverException
     * @throws \OldTown\Workflow\Config\Exception\InvalidWorkflowFactoryException
     */
    protected function init(array $options = [])
    {
        if (array_key_exists(static::PERSISTENCE, $options)) {
            $this->persistence = $options[static::PERSISTENCE];
        }
        if (array_key_exists(static::PERSISTENCE_ARGS, $options)) {
            $this->persistenceArgs = $options[static::PERSISTENCE_ARGS];
        }

        if (array_key_exists(static::VARIABLE_RESOLVER, $options)) {
            $variableResolver = $options[static::VARIABLE_RESOLVER];
            if (null === $variableResolver) {
                $variableResolver = new DefaultVariableResolver();
            }
        } else {
            $variableResolver = new DefaultVariableResolver();
        }
        if (!$variableResolver instanceof VariableResolverInterface) {
            $errMsg = sprintf('Variable resolver not implements %s', VariableResolverInterface::class);
            throw new Exception\InvalidVariableResolverException($errMsg);
        }
        $this->variableResolver = $variableResolver;


        if (array_key_exists(static::WORKFLOW_FACTORY, $options)) {
            $workflowFactory = $options[static::WORKFLOW_FACTORY];
            if (null === $workflowFactory) {
                $workflowFactory = new UrlWorkflowFactory();
            }

            if (!$workflowFactory instanceof WorkflowFactoryInterface) {
                $errMsg = sprintf('Workflow factory not implements %s', WorkflowFactoryInterface::class);
                throw new Exception\InvalidWorkflowFactoryException($errMsg);
            }
            $workflowFactory->initDone();
            $this->factory = $workflowFactory;
        }
    }

    /**
     * Определяет была ли иницилазированна дананя конфигурация
     *
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Определяет есть ли возможность модифицировать workflow  с з
     *
     * @param string $name
     *
     * @return bool
     */
    public function isModifiable($name)
    {
        return $this->factory->isModifiable($name);
    }

    /**
     * Возвращает имя класса хранилища состяония workflow
     *
     * @return string
     */
    public function getPersistence()
    {
        return $this->persistence;
    }

    /**
     * Возвращает опции для настройки хранилища стостояния workflow
     *
     * @return array
     */
    public function getPersistenceArgs()
    {
        return $this->persistenceArgs;
    }

    /**
     *
     * @return VariableResolverInterface
     */
    public function getVariableResolver()
    {
        return $this->variableResolver;
    }

    /**
     * @param string $name
     *
     * @return WorkflowDescriptor
     *
     * @throws Exception\InvalidWorkflowDescriptorException
     */
    public function getWorkflow($name)
    {
        $workflow = $this->factory->getWorkflow($name);

        if (!$workflow instanceof WorkflowDescriptor) {
            $errMsg = 'Unknown workflow name';
            throw new Exception\InvalidWorkflowDescriptorException($errMsg);
        }

        return $workflow;
    }

    /**
     * @return \String[]
     */
    public function getWorkflowNames()
    {
        return $this->factory->getWorkflowNames();
    }

    /**
     * @return WorkflowStoreInterface
     * @throws Exception\StoreException
     */
    public function getWorkflowStore()
    {
        if ($this->workflowStore) {
            return $this->workflowStore;
        }

        try {
            $class = $this->getPersistence();
            $r = new \ReflectionClass($class);
            /** @var WorkflowStoreInterface $store */
            $store = $r->newInstance();

            $args = $this->getPersistenceArgs();
            $store->init($args);
        } catch (\Exception $e) {
            throw new Exception\StoreException($e->getMessage(), $e->getCode(), $e);
        }

        $this->workflowStore = $store;

        return $this->workflowStore;
    }

    /**
     * @param UriInterface|null $url
     *
     * @throws Exception\MethodNotSupportedException
     */
    public function load(UriInterface $url = null)
    {
        $errMsg = sprintf('%s not supported method %s', ArrayConfiguration::class, __FUNCTION__);
        throw new Exception\MethodNotSupportedException($errMsg);
    }

    /**
     * @param string $workflow
     *
     * @return bool|void
     */
    public function removeWorkflow($workflow)
    {
        $this->factory->removeWorkflow($workflow);
    }

    /**
     * @param string             $name
     * @param WorkflowDescriptor $descriptor
     * @param bool|false         $replace
     *
     * @return bool|void
     */
    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace = false)
    {
        $this->factory->saveWorkflow($name, $descriptor, $replace);
    }
}
