<?php

namespace Biplane\EnumBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * BiplaneEnumExtension.
 *
 * @author Denis Vasilev <yethee@biplane.ru>
 */
class BiplaneEnumExtension extends ConfigurableExtension
{
    /**
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        if (count($mergedConfig['serializer']['types']) > 0) {
            $definition = $container->getDefinition('biplane_enum.jms_serializer.enum_handler');
            $methods = [
                'json' => 'serializeEnumToJson',
                'xml' => 'serializeEnumToXml',
            ];

            foreach ($mergedConfig['serializer']['types'] as $type) {
                foreach ($methods as $format => $method) {
                    $definition->addTag('jms_serializer.handler', [
                        'direction' => 'serialization',
                        'type' => $type,
                        'format' => $format,
                        'method' => $method,
                    ]);
                }
            }
        } else {
            $container->removeDefinition('biplane_enum.jms_serializer.enum_handler');
        }
    }
}
