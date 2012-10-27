<?php
/*
 * This file is part of the AlphaLemon CMS Application and it is distributed
 * under the GPL LICENSE Version 2.0. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) AlphaLemon <webmaster@alphalemon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.alphalemon.com
 *
 * @license    GPL LICENSE Version 2.0
 *
 */

namespace AlphaLemon\ThemeEngineBundle\Tests\Unit\Core\Asset;

use AlphaLemon\ThemeEngineBundle\Tests\TestCase;
use AlphaLemon\ThemeEngineBundle\Core\Theme\AlTheme;

/**
 * AlThemeTest
 *
 * @author AlphaLemon <webmaster@alphalemon.com>
 */
class AlThemeTest extends TestCase
{
    /**
     * @expectedException \AlphaLemon\ThemeEngineBundle\Core\Exception\InvalidArgumentException
     */
    public function testAnExceptionIsThrownsWhenTheAlThemeNotReceiveAString()
    {
        $theme = new AlTheme(array('fake'));
    }

    public function testTheThemeNameIsAlwaysSuffixedWithBundle()
    {
        $theme = $this->setUpTheme('fake');
        $this->assertEquals('FakeBundle', $theme->getThemeName());

        $theme = $this->setUpTheme('FakeBundle');
        $this->assertEquals('FakeBundle', $theme->getThemeName());
    }

    public function testSetATemplate()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);
        $otherTemplate = $this->getMockBuilder('AlphaLemon\ThemeEngineBundle\Core\Template\AlTemplate')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->assertSame($template, $theme->getTemplate('home'));
        $theme->setTemplate('home', $otherTemplate);
        $this->assertNotSame($template, $theme->getTemplate('home'));
        $this->assertSame($otherTemplate, $theme->getTemplate('home'));
    }

    public function testAddATemplate()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals(1, count($theme));
        $this->assertEquals($template, $theme->current());
        $this->assertEquals('home', $theme->key());
        $this->assertTrue($theme->valid());
    }

    public function testRetrivingATemplateFromAnInvalidKey()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertNull($theme->getTemplate('Internal'));
    }

    public function testRetrivingATemplateFromAValidKey()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals($template, $theme->getTemplate('home'));
    }

    public function testKeyIsNormalized()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals($template, $theme->getTemplate('Home'));
    }
    
    public function testGetHomeTemplateReturnsATemplateNamedHome()
    {
        $homeTemplate = $this->setUpTemplate();
        $template = $this->setUpTemplate('Internal');
        $theme = $this->setUpTheme('FakeBundle', $template);
        $theme->addTemplate($homeTemplate);

        $this->assertEquals($homeTemplate, $theme->getHomeTemplate());
    }
    
    public function testGetHomeTemplateReturnsTheFirstTemplateWhenTheHomeOneDoesNotExist()
    {
        $template = $this->setUpTemplate('Fake');
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals($template, $theme->getHomeTemplate());
    }

    private function setUpTheme($themeName = 'FakeBundle', $template = null)
    {
        $theme = new AlTheme($themeName);;
        if(null !== $template) $theme->addTemplate($template);

        return $theme;
    }

    private function setUpTemplate($templateName = 'Home')
    {
        $template = $this->getMockBuilder('AlphaLemon\ThemeEngineBundle\Core\Template\AlTemplate')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $template->expects($this->once())
            ->method('getTemplateName')
            ->will($this->returnValue($templateName));

        return $template;
    }
}