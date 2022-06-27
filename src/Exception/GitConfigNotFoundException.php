<?php

namespace App\Exception;

class GitConfigNotFoundException extends \Exception
{
    function __construct(string $path)
    {
        parent::__construct($path);
    }
}
