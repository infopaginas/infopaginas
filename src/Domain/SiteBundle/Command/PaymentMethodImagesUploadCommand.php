<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\File;

// todo: delete this command after replacing all payment images to s3 bucket
class PaymentMethodImagesUploadCommand extends ContainerAwareCommand
{
    /* @var EntityManager $em */
    protected $em;

    /** @var MediaManager */
    private $mediaManager;

    protected function configure()
    {
        $this->setName('data:upload-payment-images');
        $this->setDescription('Upload payment method images to bucket');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->mediaManager = $this->getContainer()->get('sonata.media.manager.media');

        $paymentMethods = $this->em->getRepository(PaymentMethod::class)->findAll();

        /** @var PaymentMethod $paymentMethod */
        foreach ($paymentMethods as $paymentMethod) {
            $imagePath = 'web/redesign/img/payment/' . $paymentMethod->getType() . '.png';
            if (file_exists($imagePath)) {
                $file = new File($imagePath);
                $media = $this->createNewMediaEntryFromLocalFile($file, OxaMediaInterface::CONTEXT_PAYMENT_METHOD);
                $paymentMethod->setImage($media);
            }
        }

        $this->em->flush();
    }

    public function createNewMediaEntryFromLocalFile(File $file, $context) : Media
    {
        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext($context);
        $media->setProviderName(OxaMediaInterface::PROVIDER_IMAGE);

        $this->mediaManager->save($media, false);

        return $media;
    }
}
