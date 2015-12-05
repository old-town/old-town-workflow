<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

/**
 * Interface TypeResolverInterface
 *
 * @package OldTown\Workflow
 */
interface TypeResolverInterface
{
    /**
     * Возвращает валидатор по его типу
     *
     * @param string $type
     * @param array  $args
     *
     * @return ValidatorInterface
     */
    public function getValidator($type, array $args = []);

    /**
     *
     * @param string $type
     * @param array  $args
     *
     * @return RegisterInterface
     */
    public function getRegister($type, array $args = []);

    /**
     * Возвращает функцию
     *
     * @param string $type
     * @param array  $args
     *
     * @return FunctionProviderInterface
     */
    public function getFunction($type, array $args = []);

    /**
     * Возвращает условие
     *
     * @param string $type
     * @param array  $args
     *
     * @return ConditionInterface
     */
    public function getCondition($type, array $args = []);
}
