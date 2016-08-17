<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oxa\Sonata\TranslationBundle\Block;

use Sonata\TranslationBundle\Block\LocaleSwitcherBlockService as BaseLocaleSwitcherBlockService;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Redefine original service to change locale switcher template
 * Main reason: hide switcher layout on create new record page (display it only on edit and show page)
 *
 * Class LocaleSwitcherBlockService
 * @package Oxa\Sonata\TranslationBundle\Block
 */
class LocaleSwitcherBlockService extends BaseLocaleSwitcherBlockService
{
    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        // set new template
        $resolver->setDefaults(
            array(
                'admin'                 => null,
                'object'                => null,
                'template'              => 'OxaSonataTranslationBundle:Block:block_locale_switcher.html.twig',
                'locale_switcher_route' => null,
            )
        );
    }
}
