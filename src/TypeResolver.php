<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;
use OldTown\Workflow\Exception\WorkflowException;

/**
 * Interface TypeResolver
 *
 * @package OldTown\Workflow
 */
class TypeResolver
{
    /**
     * Хранилище условий
     *
     * @var array
     */
    protected $conditions;

    /**
     * Хранилище функций
     *
     * @var array
     */
    protected $functions;

    /**
     *
     * @var array
     */
    protected $registers;

    /**
     * Валидаторы
     *
     * @var array
     */
    protected $validators;

    /**
     * Хранит инстанс объекта данного класса
     *
     * @var TypeResolver
     */
    protected static $instance;

    /**
     * Реализация паттерна "Одиночка"
     *
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * Реализация паттерна "Одиночка"
     *
     */
    protected function __clone()
    {
    }

    /**
     * Инстанцирует объект данного класса
     *
     * @return TypeResolver
     */
    public static function getInstance()
    {
        if (null !== self::$instance) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::$instance;
    }

    /**
     * Иницирует компоненты (валидаторы, функции и т.д.)
     *
     * @return void
     */
    protected function init()
    {

    }

    /**
     * @param TypeResolver $resolver
     */
    public static function setResolver(TypeResolver $resolver)
    {
        self::$instance = $resolver;
    }


    /**
     * @return TypeResolver
     */
    public static function getResolver()
    {
        return self::getInstance();
    }

    /**
     * Возвращает валидатор по его типу
     *
     * @param string $type
     * @param array  $args
     *
     * @throws WorkflowException
     * @return ValidatorInterface
     */
    public function getValidator($type, array $args = [])
    {

    }

    /**
     *
     * @param string $type
     * @param array  $args
     *
     * @throws WorkflowException
     * @return RegisterInterface
     */
    public function getRegister($type, array $args = [])
    {

    }

    /**
     * Возвращает функцию
     *
     * @param string $type
     * @param array  $args
     *
     * @throws WorkflowException
     * @return FunctionProviderInterface
     */
    public function getFunction($type, array $args = [])
    {

    }

    /**
     * Возвращает условие
     *
     * @param string $type
     * @param array  $args
     *
     * @throws WorkflowException
     * @return ConditionInterface
     */
    public function getCondition($type, array $args = [])
    {

    }
}
