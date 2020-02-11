<?php

/*
 * This file is part of the Ivory Form Extra package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\FormExtraBundle\Twig;

use Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormExtraExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $options = [
            'node_class' => SearchAndRenderBlockNode::class,
            'is_safe'    => ['html'],
        ];

        return [
            new TwigFunction('form_javascript', null, $options),
            new TwigFunction('form_stylesheet', null, $options),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ivory_form_extra';
    }
}
