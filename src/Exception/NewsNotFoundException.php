<?php

namespace App\Exception;

use RuntimeException;

class NewsNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Указанная новость не найдена!', 404);
    }
}
