<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\InvalidDescriptorException;
use DOMDocument;
use DOMElement;

/**
 * Interface WorkflowFactoryInterface
 *
 * @package OldTown\Workflow\Loader
 */
interface  WriteXmlInterface
{
    /**
     * Создает DOMElement - эквивалентный состоянию дескриптора
     *
     * @param DOMDocument $dom
     * @return DOMElement
     * @throws InvalidDescriptorException
     */
    public function writeXml(DOMDocument $dom);
}
