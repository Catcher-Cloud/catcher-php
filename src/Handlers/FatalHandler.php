<?php

namespace Catcher\Handlers;

use Catcher\Payloads\Level;

class FatalHandler extends Handler
{
    private static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

    public function register()
    {
        \register_shutdown_function(array($this, 'handle'));
        parent::register();
    }

    public function handle(...$args)
    {
        parent::handle(...$args);

        $lastError = error_get_last();

        if ($this->isFatal($lastError)) {
            $exception = $this->logger()
                ->builder()
                ->wrap(
                    errorNumber: $lastError['type'],
                    errorString: $lastError['message'],
                    errorFile: $lastError['file'],
                    errorLine: $lastError['line'],
                    errorType: $lastError['type']
                );

            $exception->isUncaught = true;
            $this->logger()->log(Level::CRITICAL, $exception);
            unset($exception->isUncaught);
        }
    }

    /**
     * Check if the error triggered is indeed a fatal error.
     *
     * @var array $lastError Information fetched from error_get_last().
     *
     * @return bool
     */
    protected function isFatal($lastError): bool
    {
        return $lastError !== null &&
            in_array($lastError['type'], self::$fatalErrors, true) &&
            // don't log uncaught exceptions as they were handled by exceptionHandler()
            !(isset($lastError['message']) &&
                strpos($lastError['message'], 'Uncaught') === 0);
    }
}
