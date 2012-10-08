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

namespace AlphaLemon\ThemeEngineBundle\Tests\Unit\Core\Generator\Base;

use AlphaLemon\ThemeEngineBundle\Tests\TestCase;
use AlphaLemon\ThemeEngineBundle\Core\Generator\TemplateParser\AlTemplateParser;
use org\bovigo\vfs\vfsStream;

/**
 * AlTemplateParserTest
 *
 * @author AlphaLemon <webmaster@alphalemon.com>
 */
class AlGeneratorBase extends TestCase
{
    protected $root;
    protected $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->root = vfsStream::setup('root');

        $this->parser = new AlTemplateParser(vfsStream::url('root'));
    }

    protected function importDefaultTheme()
    {
        vfsStream::copyFromFileSystem(__DIR__ . '/../../../../../../../../business-website-theme-bundle/AlphaLemon/Theme/BusinessWebsiteThemeBundle/Resources/views',$this->root);
    }
}