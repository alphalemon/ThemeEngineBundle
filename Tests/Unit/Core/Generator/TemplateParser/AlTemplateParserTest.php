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

namespace AlphaLemon\ThemeEngineBundle\Tests\Unit\Core\Generator\TemplateParser;

use AlphaLemon\ThemeEngineBundle\Tests\Unit\Core\Generator\Base\AlGeneratorBase;
use AlphaLemon\ThemeEngineBundle\Core\Generator\TemplateParser\AlTemplateParser;
use org\bovigo\vfs\vfsStream;

/**
 * AlTemplateParserTest
 *
 * @author AlphaLemon <webmaster@alphalemon.com>
 */
class AlTemplateParserTest extends AlGeneratorBase
{
    protected $root;
    protected $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->root = vfsStream::setup('root');
        $this->parser = new AlTemplateParser(vfsStream::url('root'));
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function testAnExceptionIsThrownWhenInformationIsYmlMalformed()
    {
        $contents = '<div id="logo">' . PHP_EOL;
        $contents .= '{% block logo %}' . PHP_EOL;
        $contents .= '{# BEGIN-SLOT LOGO' . PHP_EOL;
        $contents .= '     repeated: site' . PHP_EOL; // Malformed here
        $contents .= '   htmlContent: |' . PHP_EOL;
        $contents .= '       <img src="/uploads/assets/media/business-website-original-logo.png" title="Progress website logo" alt="Progress website logo" />' . PHP_EOL;
        $contents .= 'END-SLOT #}' . PHP_EOL;
        $contents .= '{{ renderSlot(\'logo\') }}' . PHP_EOL;
        $contents .= '{% endblock %}' . PHP_EOL;
        $contents .= '</div>';
        file_put_contents(vfsStream::url('root/home.twig.html'), $contents);
        $this->parser->parse();
    }

    public function testAnErrorKeyIsAddedWhenAnUnrecognizedAttributeIsDeclared()
    {
        $contents = '<div id="logo">' . PHP_EOL;
        $contents .= '{% block logo %}' . PHP_EOL;
        $contents .= '{# BEGIN-SLOT LOGO' . PHP_EOL;
        $contents .= '   repeated: site' . PHP_EOL;
        $contents .= '   fake: script' . PHP_EOL;
        $contents .= '   htmlContent: |' . PHP_EOL;
        $contents .= '       <img src="/uploads/assets/media/business-website-original-logo.png" title="Progress website logo" alt="Progress website logo" />' . PHP_EOL;
        $contents .= 'END-SLOT #}' . PHP_EOL;
        $contents .= '{{ renderSlot(\'logo\') }}' . PHP_EOL;
        $contents .= '{% endblock %}' . PHP_EOL;
        $contents .= '</div>';
        file_put_contents(vfsStream::url('root/home.html.twig'), $contents);
        $information = $this->parser->parse();
        $slot = $information['home.html.twig']['slots']['logo'];
        $this->assertTrue(array_key_exists('repeated', $slot));
        $this->assertTrue(array_key_exists('htmlContent', $slot));
        $this->assertFalse(array_key_exists('fake', $slot));
        $this->assertTrue(array_key_exists('errors', $slot));
        $this->assertTrue(array_key_exists('fake', $slot['errors']));
    }

    public function testRealTheme()
    {
        $this->importDefaultTheme();
        $information = $this->parser->parse();
        $this->assertTrue(array_key_exists('rightcolumn.html.twig', $information));
        $this->assertTrue(array_key_exists('sixboxes.html.twig', $information));
        $this->assertTrue(array_key_exists('base.html.twig', $information));
        $this->assertTrue(array_key_exists('home.html.twig', $information));
        $this->assertTrue(array_key_exists('fullpage.html.twig', $information));

        $homeTemplate = $information['base.html.twig'];
        $this->assertTrue(array_key_exists('assets', $homeTemplate));
        $templateAssets = $homeTemplate['assets'];
        $this->assertCount(4, $templateAssets);
        $this->assertCount(0, $templateAssets['external_stylesheets']);
        $this->assertCount(0, $templateAssets['external_javascripts']);
        $this->assertCount(0, $templateAssets['external_stylesheets_cms']);
        $this->assertCount(0, $templateAssets['external_javascripts_cms']);
        $this->assertTrue(array_key_exists('slots', $homeTemplate));
        $this->assertCount(11, $homeTemplate['slots']);

        $homeTemplate = $information['home.html.twig'];
        $this->assertTrue(array_key_exists('assets', $homeTemplate));
        $templateAssets = $homeTemplate['assets'];
        $this->assertCount(4, $templateAssets);
        $this->assertCount(4, $templateAssets['external_stylesheets']);
        $this->assertCount(7, $templateAssets['external_javascripts']);
        $this->assertCount(1, $templateAssets['external_stylesheets_cms']);
        $this->assertCount(0, $templateAssets['external_javascripts_cms']);
        $this->assertTrue(array_key_exists('slots', $homeTemplate));
        $this->assertCount(13, $homeTemplate['slots']);

        $homeTemplate = $information['sixboxes.html.twig'];
        $this->assertTrue(array_key_exists('assets', $homeTemplate));
        $templateAssets = $homeTemplate['assets'];
        $this->assertCount(4, $templateAssets);
        $this->assertCount(4, $templateAssets['external_stylesheets']);
        $this->assertCount(6, $templateAssets['external_javascripts']);
        $this->assertCount(0, $templateAssets['external_stylesheets_cms']);
        $this->assertCount(0, $templateAssets['external_javascripts_cms']);
        $this->assertTrue(array_key_exists('slots', $homeTemplate));
        $this->assertCount(6, $homeTemplate['slots']);

        $homeTemplate = $information['rightcolumn.html.twig'];
        $this->assertTrue(array_key_exists('assets', $homeTemplate));
        $templateAssets = $homeTemplate['assets'];
        $this->assertCount(4, $templateAssets);
        $this->assertCount(4, $templateAssets['external_stylesheets']);
        $this->assertCount(6, $templateAssets['external_javascripts']);
        $this->assertCount(0, $templateAssets['external_stylesheets_cms']);
        $this->assertCount(0, $templateAssets['external_javascripts_cms']);
        $this->assertTrue(array_key_exists('slots', $homeTemplate));
        $this->assertCount(2, $homeTemplate['slots']);

        $homeTemplate = $information['fullpage.html.twig'];
        $this->assertTrue(array_key_exists('assets', $homeTemplate));
        $templateAssets = $homeTemplate['assets'];
        $this->assertCount(4, $templateAssets);
        $this->assertCount(4, $templateAssets['external_stylesheets']);
        $this->assertCount(6, $templateAssets['external_javascripts']);
        $this->assertCount(0, $templateAssets['external_stylesheets_cms']);
        $this->assertCount(0, $templateAssets['external_javascripts_cms']);
        $this->assertTrue(array_key_exists('slots', $homeTemplate));
        $this->assertCount(1, $homeTemplate['slots']);
    }

    protected function importDefaultTheme()
    {
        vfsStream::copyFromFileSystem(__DIR__ . '/../../../../../../../../business-website-theme-bundle/AlphaLemon/Theme/BusinessWebsiteThemeBundle/Resources/views',$this->root);
    }
}