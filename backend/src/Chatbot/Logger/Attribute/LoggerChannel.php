<?php

declare(strict_types=1);

namespace App\Chatbot\Logger\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class LoggerChannel
{
    public function __construct(public string $name)
    {
    }
}
