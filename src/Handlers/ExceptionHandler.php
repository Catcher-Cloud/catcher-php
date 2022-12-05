<?php

namespace Catcher\Handlers;

use Catcher\Payloads\Level;

class ExceptionHandler extends Handler
{
    public function register()
    {
        $this->previousHandler = set_exception_handler(array($this, 'handle'));
        parent::register();
    }

    /**
     * @throws \Exception
     */
    function handle(...$args)
    {
        parent::handle(...$args);

        if (count($args) < 1) {
            throw new \Exception('No exception to be passed to the exception handler.');
        }

        /** @var \Exception $exception */
        $exception = $args[0];
        $wrapedException = $this->logger()
            ->builder()
            ->wrap(
                errorNumber: $exception->getCode(),
                errorString: $exception->getMessage(),
                errorFile: $exception->getFile(),
                errorLine: $exception->getLine(),
                errorType: get_class($exception)
            );
        $exception->isUncaught = true;
        $this->logger()->log(Level::ERROR, $wrapedException);
        unset($exception->isUncaught);

        // if there was no prior handler, then we toss that exception
        if ($this->previousHandler === null) {
            throw $exception;
        }

        // otherwise we overrode a previous handler, so restore it and call it
        restore_exception_handler();
        ($this->previousHandler)($exception);
    }
}
