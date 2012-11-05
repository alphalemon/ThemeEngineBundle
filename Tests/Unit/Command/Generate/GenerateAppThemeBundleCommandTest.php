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

namespace AlphaLemon\ThemeEngineBundle\Tests\Unit\Command;

use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;

/**
 * GenerateAppThemeBundleCommandTest
 *
 * @author AlphaLemon <webmaster@alphalemon.com>
 */
class GenerateAppThemeBundleCommandTest extends GenerateCommandTest
{

    /**
     * @dataProvider getNonInteractiveCommandData
     */
    public function testNonInteractiveCommand($options, $expected)
    {
        list($namespace, $bundle, $dir, $format, $structure, $noStrict) = $expected;

        $commandOptions = array(
            'no-strict' => $noStrict,
        );

        $generator = $this->getGenerator();
        $generator
            ->expects($this->once())
            ->method('generateExt')
            ->with($namespace, $bundle, $dir, $format, $structure, $commandOptions)
        ;

        $tester = new CommandTester($this->getCommand($generator, ''));
        $tester->execute($options, array('interactive' => false));
    }

    public function getNonInteractiveCommandData()
    {
        $root = vfsStream::setup('root');

        return array(
            array(array('--dir' => vfsStream::url('root'), '--no-strict' => true, '--namespace' => 'Foo/BarBundle'), array('Foo\BarBundle', 'FooBarBundle', vfsStream::url('root/'), 'annotation', false, true)),
            array(array('--dir' => vfsStream::url('root'), '--no-strict' => true, '--namespace' => 'Foo/BarBundle', '--format' => 'yml', '--bundle-name' => 'BarBundle', '--structure' => true), array('Foo\BarBundle', 'BarBundle', vfsStream::url('root/'), 'yml', true, true)),
            array(array('--dir' => vfsStream::url('root'), '--namespace' => 'AlphaLemon/Theme/BarThemeBundle', '--format' => 'yml', '--bundle-name' => 'BarThemeBundle', '--structure' => true), array('AlphaLemon\Theme\BarThemeBundle', 'BarThemeBundle', vfsStream::url('root/'), 'yml', true, false)),
        );
    }

    protected function getCommand($generator, $input)
    {
        $command = $this
            ->getMockBuilder('AlphaLemon\ThemeEngineBundle\Command\Generate\GenerateAppThemeBundleCommand')
            ->setMethods(array('checkAutoloader', 'updateKernel'))
            ->getMock()
        ;

        $command->setContainer($this->getContainer());
        $command->setHelperSet($this->getHelperSet($input));
        $command->setGenerator($generator);

        return $command;
    }

    protected function getGenerator()
    {
        // get a noop generator
        return $this
            ->getMockBuilder('AlphaLemon\ThemeEngineBundle\Core\Generator\AlAppThemeGenerator')
            ->disableOriginalConstructor()
            ->setMethods(array('generateExt'))
            ->getMock()
        ;
    }
}