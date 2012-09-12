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

namespace AlphaLemon\ThemeEngineBundle\Core\Theme;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AlActiveTheme is the object deputated to manage the active theme
 *
 * @author alphalemon <webmaster@alphalemon.com>
 */
class AlActiveTheme implements AlActiveThemeInterface
{
    private $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveTheme()
    {
        if (!file_exists($this->getActiveThemeFile()))
        {
            $themes = $this->container->get('alphalemon_theme_engine.themes');
            foreach ($themes as $theme) break;
            
            $themeName = $theme->getThemeName();
            $this->writeActiveTheme($themeName);

            return $themeName;
        }

        return file_get_contents($this->getActiveThemeFile());
    }

    /**
     * {@inheritdoc}
     */
    public function writeActiveTheme($themeName)
    {
        file_put_contents($this->getActiveThemeFile(), $themeName);
    }

    /**
     * Returns the file where the active theme is saved
     *
     * @return string
     */
    protected function getActiveThemeFile()
    {
        return $this->container->getParameter('alpha_lemon_theme_engine.active_theme_file');
    }
}