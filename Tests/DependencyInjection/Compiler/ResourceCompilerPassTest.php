<?php

/*
 * This file is part of the Ivory Form Extra package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\FormExtraBundle\Tests\DependencyInjection\Compiler;

use Ivory\FormExtraBundle\DependencyInjection\Compiler\ResourceCompilerPass;
use Ivory\FormExtraBundle\Tests\AbstractTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceCompilerPassTest extends AbstractTestCase
{
    /**
     * @var ResourceCompilerPass
     */
    private $compilerPass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compilerPass = new ResourceCompilerPass();
    }

    public function testTwigResource()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->exactly(2))
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['templating.helper.form.resources', false],
                [$parameter = 'twig.form.resources', true],
            ]));

        $containerBuilder
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo($parameter))
            ->will($this->returnValue([$template = 'layout.html.twig']));

        $containerBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter),
                $this->identicalTo([
                    '@IvoryFormExtra/Form/javascript.html.twig',
                    '@IvoryFormExtra/Form/stylesheet.html.twig',
                    $template,
                ])
            );

        $this->compilerPass->process($containerBuilder);
    }

    public function testPhpResource()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->exactly(2))
            ->method('hasParameter')
            ->will($this->returnValueMap([
                [$parameter = 'templating.helper.form.resources', true],
                ['twig.form.resources', false],
            ]));

        $containerBuilder
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo($parameter))
            ->will($this->returnValue([$template = 'layout.html.php']));

        $containerBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter),
                $this->identicalTo(['IvoryFormExtraBundle:Form', $template])
            );

        $this->compilerPass->process($containerBuilder);
    }

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerBuilderMock()
    {
        return $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasParameter', 'getParameter', 'setParameter'])
            ->getMock();
    }
}
