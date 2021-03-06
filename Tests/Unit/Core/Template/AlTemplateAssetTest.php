<?php
/**
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
use AlphaLemon\ThemeEngineBundle\Core\Template\AlTemplateAssets;

/**
 * AlTemplateAssetTest
 *
 * @author AlphaLemon <webmaster@alphalemon.com>
 */
class AlTemplateAssetTest extends TestCase
{   
    protected function setUp() 
    {
        $this->templateAssets = new AlTemplateAssets();
    }
    
    /**
     * @expectedException \AlphaLemon\ThemeEngineBundle\Core\Exception\InvalidArgumentException
     */
    public function testThemeNameThrowsAnExceptionWhenANullValueIsGiven()
    {
        $this->templateAssets->setThemeName(null);
    }
    
    /**
     * @expectedException \AlphaLemon\ThemeEngineBundle\Core\Exception\InvalidArgumentException
     */
    public function testThemeNameThrowsAnExceptionWhenANonStringValueIsGiven()
    {
        $this->templateAssets->setThemeName(3);
    }
    
    /**
     * @expectedException \AlphaLemon\ThemeEngineBundle\Core\Exception\InvalidArgumentException
     */
    public function testTemplateNameThrowsAnExceptionWhenANullValueIsGiven()
    {
        $this->templateAssets->setTemplateName(null);
    }
    
    /**
     * @expectedException \AlphaLemon\ThemeEngineBundle\Core\Exception\InvalidArgumentException
     */
    public function testTemplateNameThrowsAnExceptionWhenANonStringValueIsGiven()
    {
        $this->templateAssets->setTemplateName(array('fake'));
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testAnExceptionIsThrownCallingAMethodThatDoesNotExist()
    {
        $this->templateAssets->getImages();
    }
    
    public function testAssetsAreNotRetrievedJustValorizingTheThemeName()
    {
        $this->templateAssets->setThemeName('BusinessWebsiteThemeBundle');
        $this->assertEquals('BusinessWebsiteThemeBundle', $this->templateAssets->getThemeName());
        $this->assertCount(0, $this->templateAssets->getExternalStylesheets());
    }
    
    public function testAssetsAreNotRetrievedJustValorizingTheTemplateName()
    {
        $this->templateAssets->setTemplateName('Home');
        $this->assertCount(0, $this->templateAssets->getExternalStylesheets());
    }
    
    public function testAssetsHaveBeenSetted()
    {
        $this->templateAssets
                ->setThemeName('BusinessWebsiteThemeBundle')
                ->setTemplateName('Home');
        $this->assertCount(0, $this->templateAssets->getExternalStylesheets());
    }
    
    public function testSetExternalStylesheetsFromAStringAsset()
    {
        $assets = '@BusinessWebsiteThemeBundle/Resources/public/css/reset.css';
        
        $this->templateAssets->setExternalStylesheets($assets);
        $this->assertEquals(array($assets), $this->templateAssets->getExternalStylesheets());   
        $this->assertCount(1, $this->templateAssets->getExternalStylesheets());
    }
    
    public function testSetExternalStylesheets()
    {
        $assets = array('@BusinessWebsiteThemeBundle/Resources/public/css/reset.css');
        
        $this->templateAssets->setExternalStylesheets($assets);
        $this->assertEquals($assets, $this->templateAssets->getExternalStylesheets());        
        $this->assertCount(1, $this->templateAssets->getExternalStylesheets());  
    }
}