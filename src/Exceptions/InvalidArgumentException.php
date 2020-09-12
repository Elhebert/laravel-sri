<?php

namespace Elhebert\SubresourceIntegrity\Exceptions;

use Throwable;

class InvalidArgumentException extends Throwable implements \Psr\SimpleCache\InvalidArgumentException
{
}
