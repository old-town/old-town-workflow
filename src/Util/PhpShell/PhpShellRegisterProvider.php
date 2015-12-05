<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util\PhpShell;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\WorkflowContextInterface;
use OldTown\Workflow\RegisterInterface;


/**
 * Class PhpShellRegisterProvider
 *
 * @package OldTown\Workflow\Util\PhpShell
 */
class  PhpShellRegisterProvider implements RegisterInterface, PhpShellProviderInterface
{
    /**
     *
     * @param WorkflowContextInterface $context
     * @param WorkflowEntryInterface   $entry
     * @param array                    $args
     * @param PropertySetInterface     $ps
     *
     * @return bool
     * @throws WorkflowException
     */
    public function registerVariable(WorkflowContextInterface $context, WorkflowEntryInterface $entry, array $args = [], PropertySetInterface $ps)
    {
        $script = array_key_exists(static::PHP_SCRIPT, $args) ? $args[static::PHP_SCRIPT] : '';

        try {
            $i = new Interpreter($script);

            $i->setContextParam('entry', $entry);
            $i->setContextParam('context', $context);
            $i->setContextParam('propertySet', $ps);
            $i->setContextParam('args', $args);

            $result = $i->evalScript();
        } catch (\Exception $e) {
            $errMsg = 'Error in validator';
            throw new WorkflowException($errMsg, $e->getCode(), $e);
        }

        return $result;
    }
}
