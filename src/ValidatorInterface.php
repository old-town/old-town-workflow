<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InvalidInputException;
use OldTown\Workflow\TransientVars\TransientVarsInterface;

/**
 * Interface ValidatorRemoteInterface
 *
 * @package OldTown\Workflow
 */
interface ValidatorInterface
{
    /**
     * @param TransientVarsInterface            $transientVars
     * @param             $args
     * @param PropertySetInterface $ps
     *
     * @throws InvalidInputException
     *
     * @return void
     */
    public function validate(TransientVarsInterface $transientVars, array $args = [], PropertySetInterface $ps);
}
