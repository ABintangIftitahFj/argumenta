<?php

namespace App\Exceptions;

use Exception;

class QuotaExceededException extends Exception
{
    public function __construct(string $message = 'Batas kuota evaluasi AI Anda telah tercapai. Silakan tingkatkan paket Anda.')
    {
        parent::__construct($message, 429);
    }
}
