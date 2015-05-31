<?php

namespace Nabu\Exceptions;

class Unauthorized401 extends APIException {
    protected $httpCode = 401;
    protected $message = 'Unauthorized';
}