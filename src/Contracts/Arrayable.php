<?php

namespace Catcher\Contracts;

interface Arrayable
{
    /**
     * Transform object to array.
     *
     * @return array
     */
    function toArray(): array;
}
