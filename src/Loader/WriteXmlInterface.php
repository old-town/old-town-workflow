<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

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
     *
     * @return DOMElement
     */
    public function writeXml(DOMDocument $dom = null);
}
