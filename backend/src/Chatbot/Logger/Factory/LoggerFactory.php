<?php

declare(strict_types=1);

namespace App\Chatbot\Logger\Factory;

use function sprintf;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public function __construct(protected readonly string $kernelLogsDir, protected readonly string $kernelEnvironment)
    {
    }

    public function getLogger(string $channel): LoggerInterface
    {
        $logger = new Logger($channel);

        $logFile = sprintf('%s/chatbot_%s.%s.log', $this->kernelLogsDir, $channel, $this->kernelEnvironment);
        $handler = new RotatingFileHandler(filename: $logFile, level: Level::Debug);

        $logger->pushHandler($handler);

        return $logger;
    }
}
