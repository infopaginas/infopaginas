<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class AmazonAffiliateItemAdmin extends OxaAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('embeddedHTML', null, [
                'label' => 'Embedded HTML',
            ]);
    }
}
