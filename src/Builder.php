<?php

namespace Catcher;

use Catcher\Wrappers\ErrorWrapper;

class Builder
{
    public function wrap(
        int $errorNumber,
        string $errorString,
        string $errorFile,
        int $errorLine,
        ?string $errorType = null
    ): ErrorWrapper {
        return new ErrorWrapper(
            code: $errorNumber,
            line: $errorLine,
            file: $errorFile,
            error: $errorString,
            type: $errorType
        );
    }
}
