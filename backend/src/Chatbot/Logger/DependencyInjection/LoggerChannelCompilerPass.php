<?php

declare(strict_types=1);

namespace App\Chatbot\Logger\DependencyInjection;

use App\Chatbot\Logger\Attribute\LoggerChannel;
use App\Chatbot\Logger\Contracts\ChannelAwareLoggerInterface;
use App\Chatbot\Logger\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LoggerChannelCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $serviceDefinition) {
            if (!$serviceDefinition->hasTag(ChannelAwareLoggerInterface::class)) {
                continue;
            }

            if (!$class = $serviceDefinition->getClass()) {
                continue;
            }

            $reflectionClass = new ReflectionClass($class);

            foreach ($reflectionClass->getConstructor()?->getParameters() ?? [] as $parameter) {
                $attributes = $parameter->getAttributes(LoggerChannel::class);
                if (!empty($attributes)) {
                    $channelName = $attributes[0]->newInstance()->name;
                    $loggerServiceId = 'monolog.logger.chatbot_'.$channelName;

                    if (!$container->hasDefinition($loggerServiceId)) {
                        $container
                            ->register($loggerServiceId, LoggerInterface::class)
                            ->setFactory([new Reference(LoggerFactory::class), 'getLogger'])
                            ->addArgument($channelName)
                        ;
                    }

                    $serviceDefinition->setArgument('$'.$parameter->getName(), new Reference($loggerServiceId));
                }
            }
        }
    }
}
