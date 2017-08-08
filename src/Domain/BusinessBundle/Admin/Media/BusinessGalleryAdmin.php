<?php

namespace Domain\BusinessBundle\Admin\Media;

use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;

class BusinessGalleryAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('media.name')
            ->add('businessProfile.name')
            ->add('isPrimary')
            ->add('isActive')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('media', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessGallery/list_image.html.twig'
            ])
            ->add('media.name')
            ->addIdentifier('description')
            ->add('businessProfile')
            ->add('isPrimary', null, ['editable' => true])
            ->add('isActive', null, ['editable' => true])
            ->add('sorting', null, ['template' => 'OxaSonataAdminBundle:CRUD:list_sorting.html.twig'])
        ;
        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // show this field if we edit this page on this page itself
        // (not through relation from other admin page)
        if ($this->getRoot()->getClass() == $this->getClass()) {
            $formMapper->add('businessProfile', null, [
                'required' => true,
            ]);
        }

        $formMapper
            ->add('media', 'sonata_type_model_list', ['required' => true], ['link_parameters' => [
                'required' => true,
                'context' => 'business_profile_images',
                'provider' => 'sonata.media.provider.image',
                'allow_switch_context' => false
            ]])
            ->add('description', null, ['attr' => [
                'rows'          => 2,
                'cols'          => 100,
                'required'      => true,
                'style'         => 'resize: none',
                'placeholder'   => 'Create an image description as ' .
                    'if you were describing the image to someone who cannot see it',
            ]])
            ->add('isPrimary')
            ->add('isActive')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('media', null, [
                'template' => 'DomainBusinessBundle:Admin:BusinessGallery/show_image.html.twig'
            ])
            ->add('media.name')
            ->add('businessProfile')
            ->add('description')
            ->add('isPrimary')
            ->add('isActive')
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     * @return null
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        /** @var BusinessGallery $object */
        $imagesCount = count($object->getBusinessProfile()->getImages());

        if ($imagesCount > BusinessGallery::MAX_IMAGES_PER_BUSINESS) {
            $businessProfileName = $object->getBusinessProfile()
                ->getTranslation('name', $this->getRequest()->getLocale());

            $errorElement->with('businessProfile')
                ->addViolation($this->getTranslator()->trans(
                    'form.business_gallery.max_images',
                    [
                        'business_profile' => $businessProfileName,
                        'max_images_per_business' => BusinessGallery::MAX_IMAGES_PER_BUSINESS
                    ],
                    'AdminDomainBusinessBundle'
                ))
                ->end()
            ;
        }
    }
}
