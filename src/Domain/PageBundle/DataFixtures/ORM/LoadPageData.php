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
                'title'         => 'About Us',
                'code'          => PageInterface::CODE_ABOUT_AS,
                'description'   => 'About Us Description',
                'body'          => 'About Us Body',
                'isPublished'   => true,
                'slug'          => 'about-us',
            ],
            [
                'title'         => 'Contact Us',
                'code'          => PageInterface::CODE_CONTACT_US,
                'description'   => 'Contact Us Description',
                'body'          => 'Contact Us Body',
                'isPublished'   => true,
                'slug'          => 'contact-us',
            ],
            [
                'title'         => 'Privacy Statement',
                'code'          => PageInterface::CODE_PRIVACY_STATEMENT,
                'description'   => 'Privacy Statement Description',
                'body'          => 'Privacy Statement Body',
                'isPublished'   => true,
                'slug'          => 'privacy',
            ],
            [
                'title'         => 'Terms of Usage',
                'code'          => PageInterface::CODE_TERMS_OF_USE,
                'description'   => 'Terms of Usage Description',
                'body'          => 'Terms of Usage Body',
                'isPublished'   => true,
                'slug'          => 'terms',
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
        $object->setTitle($data['title']);
        $object->setCode($data['code']);
        $object->setDescription($data['description']);
        $object->setBody($data['body']);
        $object->setIsPublished($data['isPublished']);
        $object->setSlug($data['slug']);

        $this->addTranslation(new PageTranslation(), 'title', sprintf('Spain %s', $data['title']), $object);
        $this->addTranslation(new PageTranslation(), 'description', sprintf('Spain %s', $data['description']), $object);
        $this->addTranslation(new PageTranslation(), 'body', sprintf('Spain %s', $data['body']), $object);

        $this->manager->persist($object);

        return $object;
    }
}
