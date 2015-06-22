<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use Traversable;

/**
 * Class ValidationHelper
 * @package OldTown\Workflow\Loader
 */
class ValidationHelper
{
    /**
     * Валидация вложенных дескрипторов
     *
     * @param array|Traversable $c
     * @throws InvalidWorkflowDescriptorException
     */
    public static function validate($c)
    {
        if ($c && (is_array($c) || $c instanceof Traversable)) {
            foreach ($c as $o) {
                if ($o instanceof ValidateDescriptorInterface || (is_object($o) && method_exists($o, 'validate'))) {
                    $o->validate();
                }
            }
        }
    }
}
