<?php

namespace Catcher;

use Catcher\Handlers\ErrorHandler;
use Catcher\Handlers\ExceptionHandler;
use Catcher\Handlers\FatalHandler;
use Catcher\Handlers\Handler;
use Catcher\Payloads\Level;

class Catcher
{
    private static Logger|null $logger = null;
    private static Handler|null $fatalHandler = null;
    private static Handler|null $errorHandler = null;
    private static Handler|null $exceptionHandler = null;

    public static function init(
        array|Config $config,
        bool $handleExceptions = true,
        bool $handleErrors = true,
        bool $handleFatals = true
    ): void
    {
        $setupHandlers = is_null(self::$logger);

        self::setLogger($config);

        if ($setupHandlers) {
            if ($handleExceptions) {
                self::setupExceptionHandling();
            }
            if ($handleErrors) {
                self::setupErrorHandling();
            }
            if ($handleFatals) {
                self::setupFatalHandling();
            }
//            self::setupBatchHandling();
        }
    }

    /**
     * Return the logger.
     *
     * @return Logger|null
     */
    public static function logger(): ?Logger
    {
        return self::$logger;
    }

    private static function setLogger(array|Config $config): void
    {
        if(is_array($config)) {
            $config = new Config($config);
        }

        self::$logger = new Logger($config);
    }

    public static function log($level, $data): ?Response
    {
        return self::$logger->log($level, $data);
    }

    public static function debug($data): ?Response
    {
        return self::log(Level::DEBUG, $data);
    }

    public static function info($data): ?Response
    {
        return self::log(Level::INFO, $data);
    }

    public static function warning($data): ?Response
    {
        return self::log(Level::WARNING, $data);
    }

    public static function error($data): ?Response
    {
        return self::log(Level::ERROR, $data);
    }

    public static function critical($data): ?Response
    {
        return self::log(Level::CRITICAL, $data);
    }

    public static function setupExceptionHandling(): void
    {
        self::$exceptionHandler = new ExceptionHandler(self::$logger);
        self::$exceptionHandler->register();
    }

    public static function setupErrorHandling(): void
    {
        self::$errorHandler = new ErrorHandler(self::$logger);
        self::$errorHandler->register();
    }

    public static function setupFatalHandling(): void
    {
        self::$fatalHandler = new FatalHandler(self::$logger);
        self::$fatalHandler->register();
    }
}
