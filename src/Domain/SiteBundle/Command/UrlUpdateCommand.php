<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\VO\Url;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UrlUpdateCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    protected $items = [];

    protected $total = 0;

    protected function configure()
    {
        // this command is needed only to migrate urls see https://jira.oxagile.com/browse/INFT-3132
        // and should be removed after it
        $this->setName('data:url:update');
        $this->setDescription('Update url');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $output->writeln('Started...');

        $this->updateUrls();

        foreach ($this->getUrlFields() as $oldField => $newField) {
            if (array_key_exists($oldField, $this->items)) {
                $result = $this->items[$oldField];
            } else {
                $result = 0;
            }

            $output->writeln(sprintf('Updated "%s": %s', $oldField, $result));
        }

        $output->writeln(sprintf('Total businesses: %s', $this->total));
        $output->writeln('Done');
    }

    protected function updateUrls()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getAllBusinessProfilesIterator();
        $batchSize = 20;
        $i = 0;

        foreach ($businesses as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];

            foreach ($this->getUrlFields() as $oldField => $newField) {
                $this->updateUrlField($business, $oldField, $newField);
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i ++;

            $this->total++;
        }

        $this->em->flush();
    }

    private function updateUrlField($business, $oldField, $newField)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $oldValue = $accessor->getValue($business, $oldField);

        if ($oldValue) {
            $url = $this->createUrl($oldValue);
            $accessor->setValue($business, $newField, $url);

            if (array_key_exists($oldField, $this->items)) {
                $this->items[$oldField]++;
            } else {
                $this->items[$oldField] = 1;
            }
        }
    }

    /**
     * @param string $value
     *
     * @return Url
     */
    private function createUrl($value)
    {
        $url = new Url();

        $url->setUrl($value);

        return $url;
    }

    private function getUrlFields()
    {
        return [
            'website' => BusinessProfile::BUSINESS_PROFILE_FIELD_WEBSITE_TYPE,
            'actionUrl' => BusinessProfile::BUSINESS_PROFILE_FIELD_ACTION_URL_TYPE,
            'twitterURL' => BusinessProfile::BUSINESS_PROFILE_FIELD_TWITTER_URL_TYPE,
            'facebookURL' => BusinessProfile::BUSINESS_PROFILE_FIELD_FACEBOOK_URL_TYPE,
            'googleURL' => BusinessProfile::BUSINESS_PROFILE_FIELD_GOOGLE_URL_TYPE,
            'youtubeURL' => BusinessProfile::BUSINESS_PROFILE_FIELD_YOUTUBE_URL_TYPE,
            'instagramURL' => BusinessProfile::BUSINESS_PROFILE_FIELD_INSTAGRAM_URL_TYPE,
            'tripAdvisorURL' => BusinessProfile::BUSINESS_PROFILE_FIELD_TRIP_ADVISOR_URL_TYPE,
            'linkedInURL' => BusinessProfile::BUSINESS_PROFILE_FIELD_TRIP_LINKEDIN_URL_TYPE,
        ];
    }
}
