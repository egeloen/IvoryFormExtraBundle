<?php

/*
 * This file is part of the Ivory Form Extra package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\FormExtraBundle\Tests\Templating;

use Ivory\FormExtraBundle\Templating\FormExtraHelper;
use Ivory\FormExtraBundle\Tests\AbstractTestCase;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Templating\TemplatingRendererEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser as TemplatingNameParser;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormExtraHelperTest extends AbstractTestCase
{
    /**
     * @var PhpEngine
     */
    private $phpEngine;

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
        $this->phpEngine = new PhpEngine(new TemplateNameParser(), new FilesystemLoader([
            __DIR__.'/../../Resources/views/Form/%name%',
            __DIR__.'/../Fixtures/views/Templating/%name%',
        ]));

        $this->formFactory = Forms::createFormFactory();
        $this->formRenderer = new FormRenderer(new TemplatingRendererEngine($this->phpEngine, ['']));
        $this->phpEngine->addHelpers(['ivory_form_extra' => new FormExtraHelper($this->formRenderer)]);
    }

    public function testDefaultJavascriptFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->assertEmpty($this->normalize($this->phpEngine->render('javascript.html.php', ['form' => $form])));
    }

    public function testCustomJavascriptFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'Custom');

        $expected = '<script type="text/javascript">text-javascript</script>';
        $expected .= '<script type="text/javascript">submit-javascript</script>';

        $this->assertSame(
            $expected,
            $this->normalize($this->phpEngine->render('javascript.html.php', ['form' => $form]))
        );
    }

    public function testInheritanceJavascriptFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('textarea', $this->getFormType('textarea'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'Inheritance');

        $expected = '<script type="text/javascript">text-javascript</script>';
        $expected .= '<script type="text/javascript">button-javascript</script>';

        $this->assertSame(
            $expected,
            $this->normalize($this->phpEngine->render('javascript.html.php', ['form' => $form]))
        );
    }

    public function testDefaultStylesheetFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->assertEmpty($this->normalize($this->phpEngine->render('stylesheet.html.php', ['form' => $form])));
    }

    public function testCustomStylesheetFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('text', $this->getFormType('text'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'Custom');

        $expected = '<style type="text/css">text-stylesheet</style>';
        $expected .= '<style type="text/css">submit-stylesheet</style>';

        $this->assertSame(
            $expected,
            $this->normalize($this->phpEngine->render('stylesheet.html.php', ['form' => $form]))
        );
    }

    public function testInheritanceStylesheetFragment()
    {
        $form = $this->formFactory->createBuilder()
            ->add('textarea', $this->getFormType('textarea'))
            ->add('submit', $this->getFormType('submit'))
            ->getForm()
            ->createView();

        $this->formRenderer->setTheme($form, 'Inheritance');

        $expected = '<style type="text/css">text-stylesheet</style>';
        $expected .= '<style type="text/css">button-stylesheet</style>';

        $this->assertSame(
            $expected,
            $this->normalize($this->phpEngine->render('stylesheet.html.php', ['form' => $form]))
        );
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

    /**
     * @param string $output
     *
     * @return string
     */
    private function normalize($output)
    {
        return trim(preg_replace('/>\s+</', '><', preg_replace('/\s+/', ' ', $output)));
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TemplateNameParser extends TemplatingNameParser
{
    /**
     * {@inheritdoc}
     */
    public function parse($name)
    {
        if (is_string($name) && strpos($name, ':') !== false) {
            list($theme, $template) = explode(':', $name);

            if (empty($theme)) {
                $name = $template;
            } else {
                $name = $theme.'/'.$template;
            }
        }

        return parent::parse($name);
    }
}
