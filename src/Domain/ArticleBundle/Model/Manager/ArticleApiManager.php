<?php

namespace Domain\ArticleBundle\Model\Manager;

use Domain\ArticleBundle\Entity\Article;
use Domain\ArticleBundle\Entity\Media\ArticleGallery;
use Domain\ArticleBundle\Entity\Translation\ArticleTranslation;
use Domain\ArticleBundle\Entity\Translation\Media\ArticleGalleryTranslation;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Manager\BusinessGalleryManager;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Domain\SiteBundle\Mailer\Mailer;

class ArticleApiManager
{
    const API_ARTICLE_LIST_URL = 'http://infopaginasmedia.com/rest/api/articles';
    const ALLOWED_TYPE_URL = 'infopaginas';
    const DEFAULT_API_ERROR = 'Unknown error';

    const DEFAULT_NUMBER_OF_ITEM_TO_UPDATE = 10;

    /** @var string $accessToken */
    protected $accessToken;

    /** @var int $numberOfItemToUpdate */
    protected $numberOfItemToUpdate;

    /** @var bool $updateAll */
    protected $updateAll;

    /** @var string $defaultAuthorName */
    protected $defaultAuthorName;

    /** @var string $seoCompanyName */
    protected $seoCompanyName;

    /** @var int $seoTitleMaxLength */
    protected $seoTitleMaxLength;

    /** @var int $seoDescriptionMaxLength */
    protected $seoDescriptionMaxLength;

    /** @var string $localeEng */
    protected $localeEng;

    /** @var string $localeEsp */
    protected $localeEsp;

    /** @var ContainerInterface $container */
    protected $container;

    /** @var BusinessGalleryManager $galleryManager */
    protected $galleryManager;

    /* @var Mailer $mailer */
    private $mailer;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->galleryManager = $container->get('domain_business.manager.business_gallery');
        $this->accessToken = $container->getParameter('infopaginas_media_access_token');
        $this->mailer = $container->get('domain_site.mailer');

        $seoSettings = $container->getParameter('seo_custom_settings');

        $this->defaultAuthorName = $seoSettings['default_article_author'];
        $this->seoCompanyName = $seoSettings['company_name'];
        $this->seoTitleMaxLength = $seoSettings['title_max_length'];
        $this->seoDescriptionMaxLength = $seoSettings['description_max_length'];

        $this->localeEng = strtolower(BusinessProfile::TRANSLATION_LANG_EN);
        $this->localeEsp = strtolower(BusinessProfile::TRANSLATION_LANG_ES);
    }

    /**
     * @param int $numberOfItemToUpdate
     * @param bool $updateAll
     *
     * @return int
     */
    public function updateExternalArticles($numberOfItemToUpdate, $updateAll = false)
    {
        if ((int)$numberOfItemToUpdate) {
            $this->numberOfItemToUpdate = (int)$numberOfItemToUpdate;
        } else {
            $this->numberOfItemToUpdate = self::DEFAULT_NUMBER_OF_ITEM_TO_UPDATE;
        }

        $this->updateAll = (bool)$updateAll;

        $this->updateExternalArticlesByPages();
    }

    /**
     * @param int $pageNumber
     * @param int $itemCounter
     *
     * @return int
     */
    protected function updateExternalArticlesByPages($pageNumber = 1, $itemCounter = 0)
    {
        $url = $this->getLatestArticleUrlByPage($pageNumber);

        $data = $this->doRequestData($url);

        if ($data) {
            $itemCounter = $this->handleArticlesSync($data, $itemCounter);

            if ($itemCounter < $this->numberOfItemToUpdate or $this->updateAll) {
                $pageNumber++;
                $this->updateExternalArticlesByPages($pageNumber, $itemCounter);
            }
        }
    }

    /**
     * @param int $pageNumber
     *
     * @return string
     */
    protected function getLatestArticleUrlByPage($pageNumber = 1)
    {
        return $this->getLatestArticleUrl() . '&page=' . $pageNumber;
    }

    /**
     * @return string
     */
    protected function getLatestArticleUrl()
    {
        return self::API_ARTICLE_LIST_URL . '?access_token=' . $this->accessToken;
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    protected function doRequestData($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $response = curl_exec($ch);

        if ($response) {
            $result = json_decode($response);

            if ($result and empty($result->error)) {
                return $result;
            } else {
                if ($result and !empty($result->error)) {
                    $error = $result->error;
                } else {
                    $error = self::DEFAULT_API_ERROR;
                }

                $this->sendErrorNotification($error);
            }
        }

        return null;
    }

    /**
     * @param string $error
     */
    protected function sendErrorNotification($error)
    {
        $admins = $this->em->getRepository(User::class)->findByRole('ROLE_ADMINISTRATOR');

        $this->mailer->sendArticlesApiErrorEmailMessage($error, $admins);
    }

    /**
     * @param array $data
     * @param int $itemCounter
     *
     * @return int
     */
    protected function handleArticlesSync($data, $itemCounter = 0)
    {
        $defaultCategory = $this->em->getRepository(Category::class)->findOneBy(
            [
                'code' => Category::CATEGORY_ARTICLE_CODE,
            ]
        );
        foreach ($data as $item) {
            if ($this->validateArticleFromApi($item)) {
                if ($itemCounter >= $this->numberOfItemToUpdate and !$this->updateAll) {
                    break;
                }

                $itemCounter++;

                $articles = $this->em->getRepository(Article::class)->findBy(
                    [
                        'externalId' => $item->id,
                    ]
                );

                $article = current($articles);

                if ($article) {
                    $date = null;
                    $dateUpdated = null;

                    if (!empty($item->date)
                        and strtotime($item->date)
                        and !empty($item->date_updated)
                        and strtotime($item->date_updated)
                    ) {
                        $date = new \DateTime($item->date);
                        $dateUpdated = new \DateTime($item->date_updated);
                    }

                    if ($date and $dateUpdated and $dateUpdated > $article->getCreatedAt()) {
                        $article->setCreatedAt($dateUpdated);
                        $article->setActivationDate($date);
                    } else {
                        continue;
                    }

                    foreach ($article->getImages() as $image) {
                        $article->removeImage($image);
                    }

                } else {
                    $article = $this->createArticle($item);
                }

                $mediaUrl = $this->prepareMediaUrl($item->header);
                $gallery = json_decode($item->gallery);
                $media = $this->galleryManager->uploadArticleImageFromRemoteFile($mediaUrl);

                if ($media) {
                    $article->setCategory($defaultCategory);
                    $this->em->persist($article);
                    $article->setImage($media);
                    $this->handleTranslatableFields($item, $article);
                    if ($gallery) {
                        foreach ($gallery as $galleryItem) {

                            if ($galleryItem->photoIMG && $item->header != $galleryItem->photoIMG) {
                                $articleGalleryUrl = $this->prepareMediaUrl($galleryItem->photoIMG);
                                $galleryImage = $this->galleryManager->uploadArticleImageFromRemoteFile(
                                    $articleGalleryUrl,
                                    OxaMediaInterface::CONTEXT_ARTICLE_IMAGES
                                );
                                if ($galleryImage) {
                                    $article->addImage($this->createArticleGalleryImage($galleryImage, $galleryItem));
                                }
                            }
                        }
                    }


                    $this->em->flush();
                }
            }
        }

        return $itemCounter;
    }

    /**
     * @param Media $galleryImage
     * @param $galleryItem
     * @return ArticleGallery
     */
    protected function createArticleGalleryImage(Media $galleryImage, $galleryItem)
    {
        $articleGalleryImage = new ArticleGallery();
        $translation = new ArticleGalleryTranslation();

        if (isset($galleryItem->photoTextEng)) {
            $articleGalleryImage->setDescription($galleryItem->photoTextEng);
        } else {
            $articleGalleryImage->setDescription($galleryItem->photoText);
        }

        $articleGalleryImage->setMedia($galleryImage);
        $translation->setContent($galleryItem->photoText);
        $translation->setField(ArticleGallery::TRANSLATION_FIELD_DESCRIPTION);
        $translation->setLocale(strtolower(BusinessProfile::TRANSLATION_LANG_ES));
        $translation->setObject($articleGalleryImage);

        $this->em->persist($translation);
        $this->em->persist($articleGalleryImage);

        return $articleGalleryImage;
    }

    /**
     * @param mixed $article
     *
     * @return bool
     */
    protected function validateArticleFromApi($article)
    {
        if ((!empty($article->title_esp) or !empty($article->title_eng)) and
            (!empty($article->text_esp) or !empty($article->text_eng)) and
            $article->header and $article->type_url == self::ALLOWED_TYPE_URL and !empty($article->id)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $data
     *
     * @return Article
     */
    protected function createArticle($data)
    {
        $article = new Article();

        $article->setIsExternal(true);
        $article->setExternalId($data->id);
        $article->setIsPublished(true);
        $article->setIsOnHomepage(true);

        if (!empty($data->date) and strtotime($data->date)) {
            $date = new \DateTime($data->date);
        } else {
            $date = new \DateTime();
        }

        $article->setActivationDate($date);

        return $article;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function prepareMediaUrl($url)
    {
        $parsedUrl = parse_url($url);

        if (empty($parsedUrl['scheme']) and !(empty($parsedUrl['host'])) and !(empty($parsedUrl['path']))) {
            $scheme = 'http://';
            $url = $scheme . $parsedUrl['host'] . $parsedUrl['path'];
        }

        return $url;
    }

    /**
     * @param mixed $item
     * @param Article $article
     *
     * @return Article
     */
    protected function handleTranslatableFields($item, $article)
    {
        $titleEng = '';
        $titleEsp = '';

        $textEsp = '';
        $textEng = '';

        $seoTitleEng = '';
        $seoTitleEsp = '';

        $seoDescriptionEng = '';
        $seoDescriptionEsp = '';

        if (!empty($item->title_eng)) {
            $titleEng = $this->prepareTextData($item->title_eng, Article::ARTICLE_TITLE_MAX_LENGTH);
            $this->handleTranslation($article, $titleEng, Article::ARTICLE_FIELD_TITLE, $this->localeEng);

            $seoTitleEng = $this->buildSeoTitle($titleEng);
            $this->handleTranslation($article, $seoTitleEng, Article::ARTICLE_FIELD_SEO_TITLE, $this->localeEng);
        }

        if (!empty($item->title_esp)) {
            $titleEsp = $this->prepareTextData($item->title_esp, Article::ARTICLE_TITLE_MAX_LENGTH);
            $this->handleTranslation($article, $titleEsp, Article::ARTICLE_FIELD_TITLE, $this->localeEsp);

            $seoTitleEsp = $this->buildSeoTitle($titleEsp);
            $this->handleTranslation($article, $seoTitleEsp, Article::ARTICLE_FIELD_SEO_TITLE, $this->localeEsp);
        }

        if (!empty($item->text_eng)) {
            $textEng = $this->prepareTextData($item->text_eng, Article::ARTICLE_BODY_MAX_LENGTH);
            $this->handleTranslation($article, $textEng, Article::ARTICLE_FIELD_BODY, $this->localeEng);

            $seoDescriptionEng = $this->buildSeoDescription($textEng);
            $this->handleTranslation(
                $article,
                $seoDescriptionEng,
                Article::ARTICLE_FIELD_SEO_DESCRIPTION,
                $this->localeEng
            );
        }

        if (!empty($item->text_esp)) {
            $textEsp = $this->prepareTextData($item->text_esp, Article::ARTICLE_BODY_MAX_LENGTH);
            $this->handleTranslation($article, $textEsp, Article::ARTICLE_FIELD_BODY, $this->localeEsp);

            $seoDescriptionEsp = $this->buildSeoDescription($textEsp);
            $this->handleTranslation(
                $article,
                $seoDescriptionEsp,
                Article::ARTICLE_FIELD_SEO_DESCRIPTION,
                $this->localeEsp
            );
        }

        if ((!$article->getSlug() and $titleEsp) or !$titleEng) {
            $article->setTitle($titleEsp);
        } else {
            $article->setTitle($titleEng);
        }

        if ($textEng) {
            $article->setBody($textEng);
        } else {
            $article->setBody($textEsp);
        }

        if ($seoTitleEng) {
            $article->setSeoTitle($seoTitleEng);
        } else {
            $article->setSeoTitle($seoTitleEsp);
        }

        if ($seoDescriptionEng) {
            $article->setSeoDescription($seoDescriptionEng);
        } else {
            $article->setSeoDescription($seoDescriptionEsp);
        }

        if (!empty($item->author_name)) {
            $authorName = $this->prepareTextData($item->author_name, Article::ARTICLE_AUTHOR_MAX_LENGTH);

            $article->setAuthorName($authorName);
        } else {
            $article->setAuthorName($this->defaultAuthorName);
        }

        // workaround for spanish slug
        if (!$article->getSlug()) {
            $this->em->flush();

            if ($titleEng) {
                $article->setTitle($titleEng);
            }
        }

        return $article;
    }

    /**
     * @param string $data
     * @param int $maxLength
     *
     * @return string
     */
    protected function prepareTextData($data, $maxLength)
    {
        $text = mb_substr($data, 0, $maxLength);

        return $text;
    }

    /**
     * @param Article $article
     * @param string $content
     * @param string $field
     * @param string $locale
     *
     * @return Article
     */
    protected function handleTranslation(Article $article, $content, $field, $locale)
    {
        $translation = $article->getTranslationItem($field, $locale);

        if ($translation) {
            $translation->setContent($content);
        } else {
            $translation = new ArticleTranslation();

            $translation->setField($field);
            $translation->setLocale($locale);
            $translation->setContent($content);
            $translation->setObject($article);

            $this->em->persist($translation);
        }
    }

    /**
     * @param string $title
     *
     * @return string
     */
    protected function buildSeoTitle($title)
    {
        $seoTitle = $title . ' | ' . $this->seoCompanyName;

        $seoTitle = mb_substr($seoTitle, 0, $this->seoTitleMaxLength);

        return $seoTitle;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function buildSeoDescription($text)
    {
        $seoDescription = strip_tags($text);

        $seoDescription = mb_substr($seoDescription, 0, $this->seoDescriptionMaxLength);

        return $seoDescription;
    }
}
