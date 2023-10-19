<?php

declare(strict_types=1);

namespace In2code\In2publishCore\Component\Core\DependencyInjection;

use In2code\In2publishCore\Component\Core\PreProcessing\TcaPreProcessingService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function interface_exists;

/**
 * @codeCoverageIgnore
 */
class TcaPreProcessorPass implements CompilerPassInterface
{
    /** @var string */
    private $tagName;

    public function __construct(string $tagName)
    {
        $this->tagName = $tagName;
    }

    public function process(ContainerBuilder $container): void
    {
        $registryDefinition = $container->findDefinition(TcaPreProcessingService::class);
        if (!$registryDefinition) {
            return;
        }

        foreach ($container->findTaggedServiceIds($this->tagName) as $serviceName => $tags) {
            $definition = $container->findDefinition($serviceName);
            if ($definition->isAbstract() || interface_exists($definition->getClass())) {
                continue;
            }
            $definition->setPublic(true);

            $registryDefinition->addMethodCall('register', [
                new Reference($serviceName),
            ]);
        }
    }
}