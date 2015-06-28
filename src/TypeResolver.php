<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Workflow\Exception\WorkflowException;
use \OldTown\Workflow\Util\PhpShell\PhpShellCondition;


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
        $this->registers = [

        ];
        $this->validators = [
            'phpshell' => PhpShellCondition::class
        ];
        $this->conditions = [
            'phpshell' => PhpShellCondition::class
        ];
        $this->functions = [

        ];
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
        $className = null;
        if (array_key_exists($type, $this->validators)) {
            $className = $this->validators[$type];
        } elseif (array_key_exists(WorkflowInterface::CLASS_NAME, $args)) {
            $className = $args[WorkflowInterface::CLASS_NAME];
        }

        if (null === $className) {
            $type = (string)$type;
            $errMsg = sprintf(
                'Нет типа(%s) или аргумента class.name',
                $type
            );
            throw new WorkflowException($errMsg);
        }

        if (!class_exists($className)) {
            $errMsg = sprintf(
                'Отсутствует класс %s',
                $className
            );
            throw new WorkflowException($errMsg);
        }

        $validator = new $className();

        if (!$validator instanceof ValidatorInterface) {
            $errMsg = sprintf(
                'Validator должен реализовывать интерфейс %s',
                ValidatorInterface::class
            );
            throw new WorkflowException($errMsg);
        }

        return $validator;
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
        $className = null;
        if (array_key_exists($type, $this->registers)) {
            $className = $this->registers[$type];
        } elseif (array_key_exists(WorkflowInterface::CLASS_NAME, $args)) {
            $className = $args[WorkflowInterface::CLASS_NAME];
        }

        if (null === $className) {
            $type = (string)$type;
            $errMsg = sprintf(
                'Нет типа(%s) или аргумента class.name',
                $type
            );
            throw new WorkflowException($errMsg);
        }

        if (!class_exists($className)) {
            $errMsg = sprintf(
                'Отсутствует класс %s',
                $className
            );
            throw new WorkflowException($errMsg);
        }

        $register = new $className();

        if (!$register instanceof RegisterInterface) {
            $errMsg = sprintf(
                'Register должен реализовывать интерфейс %s',
                RegisterInterface::class
            );
            throw new WorkflowException($errMsg);
        }

        return $register;
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
        $className = null;
        if (array_key_exists($type, $this->functions)) {
            $className = $this->functions[$type];
        } elseif (array_key_exists(WorkflowInterface::CLASS_NAME, $args)) {
            $className = $args[WorkflowInterface::CLASS_NAME];
        }

        if (null === $className) {
            $type = (string)$type;
            $errMsg = sprintf(
                'Нет типа(%s) или аргумента class.name',
                $type
            );
            throw new WorkflowException($errMsg);
        }

        if (!class_exists($className)) {
            $errMsg = sprintf(
                'Отсутствует класс %s',
                $className
            );
            throw new WorkflowException($errMsg);
        }

        $function = new $className();

        if (!$function instanceof FunctionProviderInterface) {
            $errMsg = sprintf(
                'Function должен реализовывать интерфейс %s',
                FunctionProviderInterface::class
            );
            throw new WorkflowException($errMsg);
        }

        return $function;
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
        $className = null;
        if (array_key_exists($type, $this->conditions)) {
            $className = $this->conditions[$type];
        } elseif (array_key_exists(WorkflowInterface::CLASS_NAME, $args)) {
            $className = $args[WorkflowInterface::CLASS_NAME];
        }

        if (null === $className) {
            $type = (string)$type;
            $errMsg = sprintf(
                'Нет типа(%s) или аргумента class.name',
                $type
            );
            throw new WorkflowException($errMsg);
        }

        if (!class_exists($className)) {
            $errMsg = sprintf(
                'Отсутствует класс %s',
                $className
            );
            throw new WorkflowException($errMsg);
        }

        $condition = new $className();


        if (!$condition instanceof ConditionInterface) {
            $errMsg = sprintf(
                'Condition должен реализовывать интерфейс %s',
                ConditionInterface::class
            );
            throw new WorkflowException($errMsg);
        }

        return $condition;
    }
}
