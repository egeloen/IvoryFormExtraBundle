<?php

/*
 * This file is part of the Ivory Form Extra package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\FormExtraBundle\Templating;

use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormExtraHelper extends Helper
{
    /**
     * @var FormRendererInterface
     */
    private $renderer;

    /**
     * @param FormRendererInterface $renderer
     */
    public function __construct(FormRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param FormView $view
     * @param array    $variables
     *
     * @return string
     */
    public function javascript(FormView $view, array $variables = [])
    {
        return $this->renderer->searchAndRenderBlock($view, 'javascript', $variables);
    }

    /**
     * @param FormView $view
     * @param array    $variables
     *
     * @return string
     */
    public function stylesheet(FormView $view, array $variables = [])
    {
        return $this->renderer->searchAndRenderBlock($view, 'stylesheet', $variables);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ivory_form_extra';
    }
}
