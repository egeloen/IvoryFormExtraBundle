<?php

/*
 * This file is part of the Ivory Form Extra package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\FormExtraBundle\Tests\Twig;

use Ivory\FormExtraBundle\Tests\AbstractTestCase;
use Ivory\FormExtraBundle\Twig\FormExtraExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormExtraExtensionTest extends AbstractTestCase
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TwigRenderer
     */
    private $formRenderer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->twig = new \Twig_Environment(new \Twig\Loader\FilesystemLoader([
            __DIR__.'/../../Resources/views/Form',
            __DIR__.'/../Fixtures/views/Twig',
        ]));

        $this->formFactory = Forms::createFormFactory();
        $this->formRenderer = new FormRenderer(new TwigRendererEngine(
            ['javascript.html.twig', 'stylesheet.html.twig'],
            $this->twig
        ));

        $this->twig->addExtension(new FormExtraExtension());

//        if (!method_exists(FormExtension::class, '__get')) {
//
////            $this->twig->addExtension(new FormExtension($this->formRenderer));
//            $this->twig->addExtension(new FormExtension());
//        } else {
            $this->twig->addExtension(new FormExtension());

            $loader = $this->createMock(\Twig\RuntimeLoader\RuntimeLoaderInterface::class );
            $loader
                ->expects($this->once())
                ->method('load')
                ->with($this->identicalTo('Symfony\Component\Form\FormRenderer'))
                ->will($this->returnValue($this->formRenderer));

            $this->twig->addRuntimeLoader($loader);
//        }
    }

    public function testDefaultJavascriptFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $template = $this->twig->createTemplate('{{ form_javascript(form) }}');

        $this->assertEmpty($template->render(['form' => $form]));
    }

    public function testCustomJavascriptFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'javascript_custom.html.twig');

        $template = $this->twig->createTemplate('{{ form_javascript(form) }}');

        $expected = '<script type="text/javascript">text-javascript</script>';
        $expected .= '<script type="text/javascript">submit-javascript</script>';

        $this->assertSame($expected, $template->render(['form' => $form]));
    }

    public function testInheritanceJavascriptFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('textarea', $this->getFormType('textarea'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'javascript_inheritance.html.twig');

        $template = $this->twig->createTemplate('{{ form_javascript(form) }}');

        $expected = '<script type="text/javascript">text-javascript</script>';
        $expected .= '<script type="text/javascript">button-javascript</script>';

        $this->assertSame($expected, $template->render(['form' => $form]));
    }

    public function testDefaultStylesheetFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $template = $this->twig->createTemplate('{{ form_stylesheet(form) }}');

        $this->assertEmpty($template->render(['form' => $form]));
    }

    public function testCustomStylesheetFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'stylesheet_custom.html.twig');

        $template = $this->twig->createTemplate('{{ form_stylesheet(form) }}');

        $expected = '<style type="text/css">text-stylesheet</style>';
        $expected .= '<style type="text/css">submit-stylesheet</style>';

        $this->assertSame($expected, $template->render(['form' => $form]));
    }

    public function testInheritanceStylesheetFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('textarea', $this->getFormType('textarea'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'stylesheet_inheritance.html.twig');

        $template = $this->twig->createTemplate('{{ form_stylesheet(form) }}');

        $expected = '<style type="text/css">text-stylesheet</style>';
        $expected .= '<style type="text/css">button-stylesheet</style>';

        $this->assertSame($expected, $template->render(['form' => $form]));
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getFormType($type)
    {
        return method_exists(AbstractType::class, 'getBlockPrefix')
            ? 'Symfony\Component\Form\Extension\Core\Type\\'.ucfirst($type).'Type'
            : $type;
    }
}
