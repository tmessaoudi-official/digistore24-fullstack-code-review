<?php

declare(strict_types=1);

namespace App\Chatbot\Logger\Contracts;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface ChannelAwareLoggerInterface
{
}
