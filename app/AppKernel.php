<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            
            // These are the other bundles the SonataAdminBundle relies on
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            // And finally, the storage and SonataAdminBundle
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),

            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),

            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),

            new FM\ElfinderBundle\FMElfinderBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\TranslationBundle\SonataTranslationBundle(),
            new Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle(),
            new Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),
            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new \JMaitan\SonataMediaCropBundle\JMaitanSonataMediaCropBundle(),
            new Spraed\PDFGeneratorBundle\SpraedPDFGeneratorBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Spine\TrigonometricDQLBundle\SpineTrigonometricDQLBundle(),
            new AntiMattr\GoogleBundle\GoogleBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new Cache\AdapterBundle\CacheAdapterBundle(),
            new Happyr\GoogleSiteAuthenticatorBundle\HappyrGoogleSiteAuthenticatorBundle(),
            new Happyr\GoogleAnalyticsBundle\HappyrGoogleAnalyticsBundle(),
            new Aws\Symfony\AwsBundle(),

            // Oxa Bundles
            new Oxa\Sonata\AdminBundle\OxaSonataAdminBundle(),
            new Oxa\Sonata\UserBundle\OxaSonataUserBundle(),
            new Oxa\Sonata\MediaBundle\OxaSonataMediaBundle(),
            new Oxa\Sonata\TranslationBundle\OxaSonataTranslationBundle(),
            new Oxa\ConfigBundle\OxaConfigBundle(),
            new Oxa\GeolocationBundle\OxaGeolocationBundle(),
            new Domain\SiteBundle\DomainSiteBundle(),
            new Domain\BusinessBundle\DomainBusinessBundle(),
            new Domain\BannerBundle\DomainBannerBundle(),
            new Domain\PageBundle\DomainPageBundle(),

            new Domain\MenuBundle\DomainMenuBundle(),
            new Domain\ArticleBundle\DomainArticleBundle(),
            new Domain\SearchBundle\DomainSearchBundle(),
            new Domain\ReportBundle\DomainReportBundle(),
            new Oxa\Sonata\DoctrineORMAdminBundle\OxaSonataDoctrineORMAdminBundle(),
            new Oxa\VideoBundle\OxaVideoBundle(),
            new Oxa\DfpBundle\OxaDfpBundle(),
            // elastic search bundle
            new Oxa\ElasticSearchBundle\ElasticSearchBundle(),

            //SiteMap bundle
            new Presta\SitemapBundle\PrestaSitemapBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

   

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
