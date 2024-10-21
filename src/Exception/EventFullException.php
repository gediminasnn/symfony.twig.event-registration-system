<?php

namespace App\Exception;

use Exception;

class EventFullException extends Exception
{
    protected $message = 'Sorry, this event is full.';
}
