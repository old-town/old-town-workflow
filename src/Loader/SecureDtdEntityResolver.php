<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use DOMDocumentType;
use OldTown\Workflow\Exception\InvalidDtdSchemaException;
use Psr\Http\Message\UriInterface;

/**
 * Class SecureDtdEntityResolver
 *
 * @package OldTown\Workflow\Loader
 */
class SecureDtdEntityResolver
{
    /**
     * Правильный заголовк для uri схемы
     *
     * @var string
     */
    const VALID_URI = 'www.opensymphony.com';

    /**
     * @var UriInterface
     */
    protected $url;


    /**
     * @var string
     */
    protected static $uriClassName = '\Zend\Diactoros\Uri';

    /**
     * Путь до директории в которой находятся схемы для валидации
     *
     * @var string|null
     */
    protected $pathToSchemas;

    /**
     * Возвращает dom документ
     *
     * @param DomDocumentType $domDocumentType
     *
     * @return string
     * @throws  \OldTown\Workflow\Exception\InvalidDtdSchemaException
     */
    public function resolveEntity(DOMDocumentType $domDocumentType = null)
    {
        if (null === $domDocumentType) {
            $errMsg = 'Отсутствует DOMDocumentType';
            throw new InvalidDtdSchemaException($errMsg);
        }

        $systemId = $domDocumentType->systemId;

        $uri = static::uriFactory($systemId);

        if (!($this->isOpenSymphonyUrl($uri) && $this->isDtdReference($uri))) {
            $errMsg = sprintf('Некорректный uri doctype: %s', $systemId);
            throw new InvalidDtdSchemaException($errMsg);
        }


        $path = $uri->getPath();

        $dtd = $this->localDtd($path);

        return $dtd;
    }

    /**
     * @param $path
     *
     * @return string
     * @throws InvalidDtdSchemaException
     */
    protected function localDtd($path)
    {
        $pathStack = explode('/', $path);

        $fileName = array_pop($pathStack);

        $path = $this->getPathToSchemas();

        $pathToFile = $path . DIRECTORY_SEPARATOR . $fileName;

        if (!file_exists($pathToFile) || !is_readable($pathToFile)) {
            $errMsg = sprintf('Не найдена схема: %s', $fileName);
            throw new InvalidDtdSchemaException($errMsg);
        }

        $dtd = file_get_contents($pathToFile);
        return $dtd;
    }


    /**
     * @param UriInterface $uri
     *
     * @return boolean
     */
    protected function isOpenSymphonyUrl(UriInterface $uri)
    {
        $host = $uri->getHost();
        $host = strtolower($host);
        $host = trim($host);

        $flag = self::VALID_URI === $host;

        return $flag;
    }


    /**
     * @param UriInterface $uri
     *
     * @return boolean
     */
    protected function isDtdReference(UriInterface $uri)
    {
        $path = $uri->getPath();

        $flag = '.dtd' === substr($path, -4);

        return $flag;
    }


    /**
     * @return string
     */
    public static function getUriClassName()
    {
        return self::$uriClassName;
    }


    /**
     * @param string $uriClassName
     */
    public static function setUriClassName($uriClassName)
    {
        self::$uriClassName = $uriClassName;
    }

    /**
     * @param $uri
     * @return UriInterface
     */
    protected function uriFactory($uri)
    {
        $uriClassName = self::getUriClassName();

        $uri = new $uriClassName($uri);



        return $uri;
    }

    /**
     * Возвращает путь до директории в которой находятся схемы для валидации
     *
     * @return null|string
     */
    public function getPathToSchemas()
    {
        if (null !== $this->pathToSchemas) {
            return $this->pathToSchemas;
        }
        $this->pathToSchemas = __DIR__ . '/../../schemas';

        return $this->pathToSchemas;
    }
}
