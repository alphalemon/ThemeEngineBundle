<?php
/*
 * This file is part of the AlphaLemonPageTreeBundle and it is distributed
 * under the MIT License. In addiction, to use this bundle, you must leave
 * intact this copyright notice.
 *
 * Copyright (c) AlphaLemon <webmaster@alphalemon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://alphalemon.com
 *
 * @license    MIT License
 */

namespace AlphaLemon\ThemeEngineBundle\Core\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register the themes by their tags
 *
 * @author alphalemon <webmaster@alphalemon.com>
 */
class AlThemesCollectionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('alphalemon_theme_engine.themes')) {
            return;
        }

        $definition = $container->getDefinition('alphalemon_theme_engine.themes');
        foreach ($container->findTaggedServiceIds('alphalemon_theme_engine.themes.theme') as $id => $attributes) {
            foreach($attributes as $tagAttributes) {
                $definition->addMethodCall('addTheme', array(new Reference($id)));
            }

            $templateDefinition = $container->getDefinition($id);
            $templateId = $id . '.template';
            foreach ($container->findTaggedServiceIds($templateId) as $id => $templateAttributes) {
                foreach($templateAttributes as $templateTagAttributes) {
                    $templateDefinition->addMethodCall('addTemplate', array(new Reference($id)));
                }

                $templateSlotsId = $id . '.slots';
                $templateSlotsTag = preg_replace_callback('/([\w]+\.template\.)([\w]+)/', function($matches){ return $matches[1] . 'base.slots'; }, $id);
                $this->addSlots($container, $templateSlotsId, $templateSlotsTag);
                $this->addSlots($container, $templateSlotsId, $templateSlotsId);
            }
        }
    }

    private function addSlots(ContainerBuilder $container, $templateSlotsId, $templateSlotsTag)
    {
        $templateSlotsDefinition = $container->getDefinition($templateSlotsId);
        foreach ($container->findTaggedServiceIds($templateSlotsTag) as $id => $templateSlotsAttributes) {
            foreach($templateSlotsAttributes as $templateSlotsTagAttributes) {
                $templateSlotsDefinition->addMethodCall('addSlot', array(new Reference($id)));
            }
        }
    }
}