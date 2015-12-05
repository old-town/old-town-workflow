<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Util\PhpShell\PhpShellConditionProvider;
use OldTown\Workflow\Util\PhpShell\PhpShellFunctionProvider;
use OldTown\Workflow\Util\PhpShell\PhpShellValidatorProvider;
use OldTown\Workflow\Util\PhpShell\PhpShellRegisterProvider;

/**
 * Class TypeResolver
 *
 * @package OldTown\Workflow
 */
class TypeResolver implements TypeResolverInterface
{
    /**
     *
     * @var string
     */
    const PHP_SHELL = 'phpshell';

    /**
     *
     * @var string
     */
    const CLASS_NAME = 'class.name';

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
     *
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Иницирует компоненты (валидаторы, функции и т.д.)
     *
     * @return void
     */
    protected function init()
    {
        $this->registers = [
            static::PHP_SHELL => PhpShellRegisterProvider::class,
        ];
        $this->validators = [
            static::PHP_SHELL => PhpShellValidatorProvider::class,
        ];
        $this->conditions = [
            static::PHP_SHELL => PhpShellConditionProvider::class
        ];
        $this->functions = [
            static::PHP_SHELL => PhpShellFunctionProvider::class
        ];
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
        } elseif (array_key_exists(static::CLASS_NAME, $args)) {
            $className = $args[static::CLASS_NAME];
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
        } elseif (array_key_exists(static::CLASS_NAME, $args)) {
            $className = $args[static::CLASS_NAME];
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
        } elseif (array_key_exists(static::CLASS_NAME, $args)) {
            $className = $args[static::CLASS_NAME];
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
        } elseif (array_key_exists(static::CLASS_NAME, $args)) {
            $className = $args[static::CLASS_NAME];
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
