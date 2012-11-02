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

namespace AlphaLemon\ThemeEngineBundle\Tests\Unit\Core\Controller\Listener;

use AlphaLemon\ThemeEngineBundle\Tests\TestCase;
use AlphaLemon\ThemeEngineBundle\Core\Rendering\Controller\FrontendController;

class FrontendControllerTester extends FrontendController
{
}


/**
 * FrontendControllerTest
 *
 * @author AlphaLemon <webmaster@alphalemon.com>
 */
class FrontendControllerTest extends TestCase
{
    private $response;

    protected function setUp()
    {
        $this->response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $this->templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->container->expects($this->exactly(2))
            ->method('getParameter')
            ->will($this->onConsecutiveCalls('AcmeWebsiteBundle', 'ThemeEngineBundle:Fake:template.html.twig'));

        $this->controller = new FrontendControllerTester();
        $this->controller->setContainer($this->container);
    }

    public function testCustomErrorPageIsReturnedWhenAnExceptionIsThrownRenderingTheRequestedTemplate()
    {
        $this->setUpRequest();

        $this->templating->expects($this->at(0))
            ->method('renderResponse')
            ->will($this->throwException(new \RuntimeException));
        
        $this->templating->expects($this->at(1))
            ->method('renderResponse')
            ->will($this->returnValue($this->response));

        $this->dispatcher->expects($this->never())
            ->method('dispatch');

        $this->container->expects($this->at(0))
            ->method('get')
            ->with('request')
            ->will($this->returnValue($this->request));
        
        $this->container->expects($this->at(3))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($this->templating));
        
        $this->container->expects($this->at(4))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($this->templating));
        
        $this->controller->showAction();
    }

    public function testCustomErrorPageIsReturnedWhenAnExceptionIsThrownRenderingAListener()
    {
        $this->setUpRequest();

        $this->templating->expects($this->exactly(2))
            ->method('renderResponse')
            ->will($this->returnValue($this->response));

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->will($this->throwException(new \RuntimeException));
        
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('request')
            ->will($this->returnValue($this->request));
        
        $this->container->expects($this->at(3))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($this->templating));
        
        $this->container->expects($this->at(4))
            ->method('get')
            ->with('event_dispatcher')
            ->will($this->returnValue($this->dispatcher));
        
        $this->container->expects($this->at(5))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($this->templating));

        $this->controller->showAction();
    }

    public function testAnExceptionIsThrownWhenRenderSlotContentsMethodDoesNotReturnAnArray()
    {
        $this->setUpRequest(2);

        $this->templating->expects($this->once())
            ->method('renderResponse')
            ->will($this->returnValue($this->response));

        $this->dispatcher->expects($this->exactly(3))
            ->method('dispatch');

        $this->container->expects($this->exactly(3))
            ->method('get')
            ->will($this->onConsecutiveCalls($this->request, $this->templating, $this->dispatcher));

        $this->controller->showAction();
    }

    private function setUpRequest($times = 1)
    {
        $this->request->expects($this->exactly($times))
            ->method('getLocale')
            ->will($this->returnValue('en'));

        $this->request->expects($this->exactly($times))
            ->method('get')
            ->will($this->returnValue('index'));
    }
}
