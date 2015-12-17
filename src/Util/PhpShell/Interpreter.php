<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util\PhpShell;

use OldTown\Workflow\Exception\InvalidArgumentException;
use OldTown\Workflow\Exception\RuntimeException;


/**
 * @todo переписать на http://php.net/manual/ru/runkit.sandbox.php
 *
 * Class Interpreter
 * @package OldTown\Workflow\Util\PhpShell
 */
class  Interpreter
{
    /**
     * Код php который нужно выполнить
     *
     *
     * @var string
     */
    protected $source;

    /**
     * Разрешенные команды
     *
     * @var []
     */
    protected $allowedCalls;

    /**
     * Ошибки парсинга
     *
     * @var array
     */
    protected $parseErrors = [];

    /**
     * Параметры передаваемые в контекст запускаемого скрипта
     *
     * @var array
     */
    protected $contextParams = [];

    /**
     * Разрешенные по умолчанию команды
     *
     * @var array
     */
    private static $defaultAllowedCalls = [
        'explode',
        'implode',
        'date',
        'time',
        'round',
        'trunc',
        'rand',
        'ceil',
        'floor',
        'srand',
        'strtolower',
        'strtoupper',
        'substr',
        'stristr',
        'strpos',
        'print',
        'print_r',
        'true',
        'false',
        'null',
        'InvalidArgumentException',
        'array_key_exists'
    ];

    /**
     * Ключем является имя лексемы. Значение число. Вспомогательное свойство для проверки через array_key_exists
     *
     * @var array
     */
    protected $allowLexemeMap;

    /**
     * Флаг определяющий валидный ли скрипт
     *
     * @var boolean|null
     */
    protected $flagScriptValid;

    /**
     * Разрешенные лексемы
     *
     * @var array
     */
    protected static $allowLexeme = [
        'T_POW',
        'T_ELLIPSIS',
        'T_POW_EQUAL',
        //'T_REQUIRE_ONCE',
        //'T_REQUIRE',
        //'T_EVAL',
        //'T_INCLUDE_ONCE',
        //'T_INCLUDE',
        'T_LOGICAL_OR',
        'T_LOGICAL_XOR',
        'T_LOGICAL_AND',
        'T_PRINT',
        'T_SR_EQUAL',
        'T_SL_EQUAL',
        'T_XOR_EQUAL',
        'T_OR_EQUAL',
        'T_AND_EQUAL',
        'T_MOD_EQUAL',
        'T_CONCAT_EQUAL',
        'T_DIV_EQUAL',
        'T_MUL_EQUAL',
        'T_MINUS_EQUAL',
        'T_PLUS_EQUAL',
        'T_BOOLEAN_OR',
        'T_BOOLEAN_AND',
        'T_IS_NOT_IDENTICAL',
        'T_IS_IDENTICAL',
        'T_IS_NOT_EQUAL',
        'T_IS_EQUAL',
        'T_IS_GREATER_OR_EQUAL',
        'T_IS_SMALLER_OR_EQUAL',
        'T_SR',
        'T_SL',
        //'T_INSTANCEOF',
        //'T_UNSET_CAST',
        'T_BOOL_CAST',
        'T_OBJECT_CAST',
        'T_ARRAY_CAST',
        'T_STRING_CAST',
        'T_DOUBLE_CAST',
        'T_INT_CAST',
        'T_DEC',
        'T_INC',
        'T_CLONE',
        'T_NEW',
        //'T_EXIT',
        'T_IF',
        'T_ELSEIF',
        'T_ELSE',
        //'T_ENDIF',
        'T_LNUMBER',
        'T_DNUMBER',
        'T_STRING',
        'T_STRING_VARNAME',
        'T_VARIABLE',
        //'T_OPEN_TAG_WITH_ECHO',
        //'T_INLINE_HTML',
        'T_CHARACTER',
        //'T_BAD_CHARACTER',
        'T_ENCAPSED_AND_WHITESPACE',
        'T_CONSTANT_ENCAPSED_STRING',
        'T_ECHO',
        'T_DO',
        'T_WHILE',
        //'T_ENDWHILE',
        'T_FOR',
        //'T_ENDFOR',
        'T_FOREACH',
        //'T_ENDFOREACH',
        'T_DECLARE',
        //'T_ENDDECLARE',
        'T_AS',
        'T_SWITCH',
        //'T_ENDSWITCH',
        'T_CASE',
        'T_DEFAULT',
        'T_BREAK',
        'T_CONTINUE',
        //'T_GOTO',
        //'T_FUNCTION',
        'T_CONST',
        'T_RETURN',
        //'T_YIELD',
        //'T_TRY',
        //'T_CATCH',
        //'T_FINALLY',
        'T_THROW',
        //'T_USE',
        //'T_INSTEADOF',
        //'T_GLOBAL',
        //'T_PUBLIC',
        //'T_PROTECTED',
        //'T_PRIVATE',
        //'T_FINAL',
        //'T_ABSTRACT',
        //'T_STATIC',
        'T_VAR',
        //'T_UNSET',
        'T_ISSET',
        'T_EMPTY',
        //'T_HALT_COMPILER',
        'T_CLASS',
        //'T_TRAIT',
        //'T_INTERFACE',
        //'T_EXTENDS',
        //'T_IMPLEMENTS',
        'T_OBJECT_OPERATOR',
        'T_DOUBLE_ARROW',
        //'T_LIST',
        'T_ARRAY',
        //'T_CALLABLE',
        'T_CLASS_C',
        //'T_TRAIT_C',
        'T_METHOD_C',
        'T_FUNC_C',
        'T_LINE',
        'T_FILE',
        'T_COMMENT',
        'T_DOC_COMMENT',
        'T_OPEN_TAG',
        'T_OPEN_TAG_WITH_ECHO',
        'T_CLOSE_TAG',
        'T_WHITESPACE',
        //'T_START_HEREDOC',
        //'T_END_HEREDOC',
        'T_DOLLAR_OPEN_CURLY_BRACES',
        'T_CURLY_OPEN',
        'T_PAAMAYIM_NEKUDOTAYIM',
        //'T_NAMESPACE',
        //'T_NS_C',
        'T_DIR',
        'T_NS_SEPARATOR',
        'T_DOUBLE_COLON',
    ];

    /**
     * @return array
     */
    protected function getAllowLexemeMap()
    {
        if ($this->allowLexemeMap) {
            return $this->allowLexemeMap;
        }
        $this->allowLexemeMap = array_flip(static::$allowLexeme);


        return $this->allowLexemeMap;
    }


    /**
     * @param string $name
     * @param mixed $contextParam
     * @return $this
     */
    public function setContextParam($name, $contextParam)
    {
        $name = (string)$name;
        $this->contextParams[$name] = $contextParam;

        return $this;
    }


    /**
     * @return mixed
     *
     * @throws \OldTown\Workflow\Exception\RuntimeException
     */
    public function getAllowedCalls()
    {
        if ($this->allowedCalls) {
            return $this->allowedCalls;
        }

        $this->initAllowedCalls();
        return $this->allowedCalls;
    }

    /**
     * Инициализация спска разрешенных команд
     *
     * @return void
     *
     * @throws \OldTown\Workflow\Exception\RuntimeException
     */
    protected function initAllowedCalls()
    {
        if ($this->allowedCalls) {
            $errMsg = 'Список разрешенных команд уже инициализирован';
            throw new RuntimeException($errMsg);
        }

        $this->allowedCalls = array_combine(static::$defaultAllowedCalls, static::$defaultAllowedCalls);
    }

    /**
     * @param $source
     *
     * @throws \OldTown\Workflow\Exception\RuntimeException
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     */
    public function __construct($source)
    {
        if (!extension_loaded('tokenizer')) {
            $errMsg = 'Для работы phpshell необходимо установит tokenizer';
            throw new RuntimeException($errMsg);
        }
        if (!settype($source, 'string')) {
            $errMsg = 'Код должен быть представлен в виде строки, либо объекта который может быть представлен строкой';
            throw new InvalidArgumentException($errMsg);
        }

        $this->source = $source;
    }

    /**
     *
     * @return boolean
     *
     * @throws \OldTown\Workflow\Exception\RuntimeException
     */
    protected function getFlagScriptValid()
    {
        if (null !== $this->flagScriptValid) {
            return $this;
        }
        $source = "<?php {$this->source} ?>";
        $tokens = token_get_all($source);

        $allowLexemeMap = $this->getAllowLexemeMap();

        $allowedCalls = $this->getAllowedCalls();

        foreach ($tokens as $tokenIndex => $token) {
            if (is_array($token)) {
                $tokenId = $token[0];


                $tokenName = token_name($tokenId);

                if (!array_key_exists($tokenName, $allowLexemeMap)) {
                    $this->parseErrors[] = sprintf('Запрещенная лексема: %s', $tokenName);
                    break;
                }
                $tokenValue = $token[1];
                if (T_STRING === $tokenId && !array_key_exists($tokenValue, $allowedCalls)) {
                    $previousTokenIndex = $tokenIndex - 1;
                    if (array_key_exists($previousTokenIndex, $tokens)) {
                        $previousToken = $tokens[$previousTokenIndex];
                        if (is_array($previousToken)) {
                            $previousTokenId = $previousToken[0];
                            $previousTokenName = token_name($previousTokenId);
                            if ('T_OBJECT_OPERATOR' === $previousTokenName) {
                                continue;
                            }
                        }
                    }
                    $this->parseErrors[] = sprintf('Запрещенная функция: %s', $tokenValue);
                    break;
                }
            }
        }

        $this->flagScriptValid = 0 === count($this->parseErrors);

        return $this->flagScriptValid;
    }

    /**
     *
     * @return mixed
     *
     * @throws \OldTown\Workflow\Exception\RuntimeException
     */
    public function evalScript()
    {
        if (!$this->getFlagScriptValid()) {
            $err = implode(", \n", $this->parseErrors);
            $errMsg = sprintf('Скрипт не валидный: %s', $err);

            throw new RuntimeException($errMsg);
        }


        $source = $this->source;
        $executor = function (array $args = []) use ($source) {
            extract($args);
            ob_start();
            $result = eval($source);
            $content = ob_get_clean();

            $errorGetLast = error_get_last();
            if (null === $errorGetLast && $content) {
                echo $content;
            }

            return $result;
        };

        $result = call_user_func($executor, $this->contextParams);

        return $result;
    }
}
