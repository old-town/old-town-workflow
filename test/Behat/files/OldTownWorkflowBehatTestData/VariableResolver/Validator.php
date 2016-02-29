<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTownWorkflowBehatTestData\VariableResolver;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\ValidatorInterface;
use OldTown\Workflow\Exception\InvalidInputException;

/**
 * Class Validator
 *
 * @package OldTownWorkflowBehatTestData\VariableResolver
 */
class Validator implements ValidatorInterface
{
    /**
     * @param TransientVarsInterface $transientVars
     * @param array                  $args
     * @param PropertySetInterface   $ps
     *
     * @return bool
     */
    public function validate(TransientVarsInterface $transientVars, array $args = [], PropertySetInterface $ps)
    {
        if (array_key_exists('expected', $args) && array_key_exists('actual', $args) && $args['expected'] === $args['actual']) {
            return true;
        }

        throw new InvalidInputException('Ivalid data');
    }
}
