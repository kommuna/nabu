<?php

namespace Nabu\Exceptions;

class Conflict409 extends APIException {
    protected $httpCode = 409;
    protected $message = 'Conflict';
}
