<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\InvalidParsingWorkflowException;
use DOMElement;
use Psr\Http\Message\UriInterface;

/**
 * Class WorkflowLoader
 *
 * @package OldTown\Workflow\Loader
 */
class WorkflowLoader
{
    /**
     * @param      $resource
     * @param bool $validate
     * @return WorkflowDescriptor
     * @throws InvalidParsingWorkflowException
     */
    public static function load($resource, $validate = true)
    {
        $content = null;
        if ($resource instanceof UriInterface) {
            $uri = $resource->__toString();
            $content = file_get_contents($uri);
        } elseif (is_string($resource)) {
            if (!file_exists($resource)) {
                $errMsg = sprintf(
                    'По пути %s отсутствует файл с workflow',
                    $resource
                );
                throw new InvalidParsingWorkflowException($errMsg);
            }
            $content = file_get_contents($resource);
        }

        if (!is_string($content)) {
            $errMsg = 'Не удалось получить workflow';
            throw new InvalidParsingWorkflowException($errMsg);
        }


        try {
            libxml_use_internal_errors(true);

            $xmlDoc = new \DOMDocument();
            $resultLoadXml = $xmlDoc->loadXML($content);

            if (!$resultLoadXml && ($error = libxml_get_last_error()) && $error instanceof \LibXMLError) {
                $errMsg = "Error in workflow xml.\n";
                $errMsg .= "Message: {$error->message}.\n";
                $errMsg .= "File: {$error->file}.\n";
                $errMsg .= "Line: {$error->line}.\n";
                $errMsg .= "Column: {$error->column}.";

                throw new InvalidParsingWorkflowException($errMsg);
            }

            /** @var DOMElement $root */
            $root = $xmlDoc->getElementsByTagName('workflow')->item(0);

            $descriptor = DescriptorFactory::getFactory()->createWorkflowDescriptor($root);

            if ($validate) {
                $descriptor->validate();
            }

            return $descriptor;
        } catch (\Exception $e) {
            $errMsg = "Ошибка при загрузке workflow из ресурса {$resource}.";
            throw new InvalidParsingWorkflowException($errMsg, $e->getCode(), $e);
        }
    }
}
