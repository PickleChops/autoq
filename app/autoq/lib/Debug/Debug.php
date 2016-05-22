<?php


namespace Autoq\Lib\Debug;

/**
 * Class Debug Simple debug class to catch errors
 * @package Lib\Debug
 */
class Debug
{
    static private $instance;

    private $enable = true;
    private $stream;

    /**
     * Singleton for Debug object
     * @param $enable
     * @param $stream
     * @return Debug
     */
    public static function initialize($enable, $stream)
    {
        if (self::$instance === null) {
            self::$instance = new self($enable, $stream);
        }

        return self::$instance;
    }

    /**
     * Debug constructor.
     * @param $enable
     * @param $stream
     */
    private function __construct($enable, $stream)
    {

        $this->enable = $enable;
        $this->stream = $stream;

        if ($enable) {

            //Register Error Handler
            set_error_handler([$this, 'errorHandler']);

            //Register Exception Handler
            set_exception_handler([$this, 'exceptionHandler']);

        }
    }

    /**
     * Error Handler
     * @param $errnum
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return bool
     * @throws \Exception
     */
    public function errorHandler($errnum, $errstr, $errfile, $errline)
    {
        // the error was supressed using @, so do nothing
        if (error_reporting() == 0) {
            return true;
        }

        if ($this->enable) {

            $debugError = new DebugError($errstr);
            $debugError->setCode($errnum)->setFile($errfile)->setLine($errline);
            $debugError->setTrace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

            $this->baseHandler($debugError);

        }

        return true;

    }

    /**
     * Exception Handler
     * @param \Exception $e
     * @return DebugException|null
     */
    public function exceptionHandler(\Exception $e)
    {
        if ($this->enable) {
            $debugException = new DebugException($e);
            $this->baseHandler($debugException);
        }

        return true;

    }

    /**
     * Handle as required
     * @param DebugEventInterface $debugEvent
     */
    private function baseHandler(DebugEventInterface $debugEvent)
    {
        //Get the details about the error
        $errorDetails = $this->buildErrorResponse($debugEvent);

        fwrite($this->stream, $this->formatResponseForStream($errorDetails));

        exit(1);
        
    }

    private function formatResponseForStream($errorDetails) {
        $return = '';
        $return .= "\n\tSorry, an error has occurred.\n";
        $return .= "\t\n";
        $return .= "\tserver: {$errorDetails['hostname']}\n";
        $return .= "\tPID: {$errorDetails['pid']}\n";
        $return .= "\tdate and time: " . date(DATE_RFC822, $errorDetails['time']) . "\n";
        $return .= "\tmodule/error message: {$errorDetails['message']}\n";
        $return .= "\terror code: {$errorDetails['code']}\n";
        $return .= "\toccurred in file/line: {$errorDetails['file']}:{$errorDetails['line']}\n";
        $return .= "\tcli: {$errorDetails['cli']}\n";
        $return .= "\tstack trace: {$errorDetails['stack']}\n\n";

        return $return;
    }

    /**
     * @param DebugEventInterface $debugEvent
     * @return array
     */
    private function buildErrorResponse(DebugEventInterface $debugEvent)
    {
        $errorMessage = $debugEvent->getMessage();

        $response = [
            'time' => time(),
            'message' => $errorMessage,
            'code' => $debugEvent->getCode(),
            'file' => $debugEvent->getFile(),
            'line' => $debugEvent->getLine(),
            'pid' => getmypid(),
            'hostname' => trim(php_uname('n'))
        ];

        /**
         * Local getter from $_SERVER
         */
        $data = $_SERVER;
        $get = function ($key) use ($data) {
            return isset($data[$key]) ? $data[$key] : '';
        };

        // Build up our response for cli
        $args = $get('argv');
        $cli = is_array($args) ? implode(' ', $args) : '';

        $cliResponse = [
            'stack' => "\n\n\t\t" . str_replace("\n", "\n\t\t", $this->formatStackTraceAsString($this->removeDebugFrames($debugEvent->getTrace()))),
            'cli' => $cli
        ];


        return $response + $cliResponse;
    }

    /**
     * Turn a PHP stacktrace into a string
     * @param $trace
     * @return string
     */
    private function formatStackTraceAsString($trace)
    {
        $output = [];

        foreach ($trace as $level => $frame) {

            //Fill in any blanks
            $frame = array_merge(array_fill_keys(['class', 'file', 'type','function', 'line'], null), $frame);

            $prefix = sprintf("#%d %s() ", $level, $frame['class'] . $frame['type'] . $frame['function']);

            $fileLineStr = $frame['file'] && $frame['line'] ? sprintf(" called at [%s:line %d]",
                $frame['file'],
                $frame['line']) : null;


            $output[] = $prefix . $fileLineStr;

        }

        return implode("\n", $output);

    }

    /**
     * Remove frames from stacktrace that are from the Debug class to remove fluff from trace
     * @param $stackTrace
     * @return string
     */
    private function removeDebugFrames($stackTrace)
    {
        $processedTrace = [];

        foreach ($stackTrace as $frame) {
            if (!array_key_exists('class', $frame) || strpos($frame['class'], 'Debug') === false) {
                $processedTrace[] = $frame;
            }
        }

        return $processedTrace;

    }


}