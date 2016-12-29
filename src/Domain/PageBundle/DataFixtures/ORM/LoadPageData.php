<?php
namespace Domain\BusinessBundle\DataFixture\Test;

use Domain\PageBundle\Entity\Page;
use Domain\PageBundle\Entity\Translation\PageTranslation;
use Domain\PageBundle\Model\PageInterface;
use Oxa\Sonata\AdminBundle\Model\Fixture\OxaAbstractFixture;

class LoadPageData extends OxaAbstractFixture
{
    protected $order = 20;

    protected function loadData()
    {
        $dataArray = [
            [
                'titleEn'       => 'Contact Us',
                'titleEs'       => 'Spanish Contact Us',
                'code'          => PageInterface::CODE_CONTACT_US,
                'descriptionEn' => 'Contact Us Description',
                'descriptionEs' => 'Spanish Contact Us Description',
                'bodyEn'        => 'Contact Us Body',
                'bodyEs'        => 'Spanish Contact Us Body',
                'isPublished'   => true,
                'slug'          => 'contact-us',
            ],
            [
                'titleEn'       => 'Privacy Policy',
                'titleEs'       => 'Política de Privacidad',
                'code'          => PageInterface::CODE_PRIVACY_STATEMENT,
                'descriptionEn' => 'Privacy Policy',
                'descriptionEs' => 'Política de Privacidad',
                'bodyEn'        => $this->container->get('twig')->render(
                    'DomainPageBundle:Fixtures:privacy-en.html.twig'
                ),
                'bodyEs'        => $this->container->get('twig')->render(
                    'DomainPageBundle:Fixtures:privacy-es.html.twig'
                ),
                'isPublished'   => true,
                'slug'          => 'privacy',
            ],
            [
                'titleEn'       => 'Terms of Service',
                'titleEs'       => 'Terminos del Servicio',
                'code'          => PageInterface::CODE_TERMS_OF_USE,
                'descriptionEn' => 'Terms of Service',
                'descriptionEs' => 'Terminos del Servicio',
                'bodyEn'        => $this->container->get('twig')->render(
                    'DomainPageBundle:Fixtures:term-of-use-en.html.twig'
                ),
                'bodyEs'        => $this->container->get('twig')->render(
                    'DomainPageBundle:Fixtures:term-of-use-es.html.twig'
                ),
                'isPublished'   => true,
                'slug'          => 'terms',
            ],
            [
                'titleEn'       => 'Advertise with Us',
                'titleEs'       => 'Spanish Advertise with Us',
                'code'          => PageInterface::CODE_ADVERTISE,
                'descriptionEn' => 'Advertise with Us Description',
                'descriptionEs' => 'Spanish Advertise with Us Description',
                'bodyEn'        => 'Advertise with Us  - <a href="/businesses/new">Get Your Free Listing</a>',
                'bodyEs'        => 'Advertise with Us  - <a href="/businesses/new">Spanish Get Your Free Listing</a>',
                'isPublished'   => true,
                'slug'          => 'advertise',
            ],
        ];

        foreach ($dataArray as $data) {
            $this->loadPage($data);
        }
    }

    /**
     * @param array $data
     * @return Page
     */
    protected function loadPage(array $data)
    {
        $object = new Page();
        $object->setTitle($data['titleEn']);
        $object->setCode($data['code']);
        $object->setDescription($data['descriptionEn']);
        $object->setBody($data['bodyEn']);
        $object->setIsPublished($data['isPublished']);
        $object->setSlug($data['slug']);

        $this->addTranslation(new PageTranslation(), 'titleEn', $data['titleEs'], $object);
        $this->addTranslation(new PageTranslation(), 'description', $data['descriptionEs'], $object);
        $this->addTranslation(new PageTranslation(), 'body', $data['bodyEs'], $object);

        $object = $this->container->get('domain_page.manager.page')->setPageSeoData($object, $this->container);

        $this->manager->persist($object);

        return $object;
    }
}
