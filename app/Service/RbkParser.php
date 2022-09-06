<?php

namespace App\Service;

use App\Constant\Number as NumberConstant;
use App\Exceptions\NotFoundException;
use App\ObjectValue\ArticleData;
use Illuminate\Support\Facades\Storage;
use voku\helper\HtmlDomParser;

use function str_replace;
use function trim;
use function strip_tags;
use function date;
use function file_get_contents;
use function explode;
use function array_pop;
use function uniqid;
use function sprintf;

/**
 * Class RbkParser
 * @package App\Service
 */
class RbkParser implements SiteParserInterface
{
    /** Селекторы для выбора нужных частей новостей */
    /** @var string  */
    private string $newsUrlSelector;
    /** @var string  */
    private string $newsContentSelector;
    /** @var string  */
    private string $categorySelector;
    /** @var string  */
    private string $datetimeSelector;
    /** @var string  */
    private string $titleSelector;
    /** @var string  */
    private string $imagesSelector;
    /** @var string  */
    private string $textOverviewSelector;
    /** @var string  */
    private string $otherTextSelector;

    /**
     * RbkParser constructor.
     * @param array $selectors
     * @throws NotFoundException
     */
    public function __construct(array $selectors)
    {
        $this->validateSelectors($selectors);
        $this->newsUrlSelector = $selectors['newsUrl'];
        $this->newsContentSelector = $selectors['newsContent'];
        $this->categorySelector = $selectors['category'];
        $this->datetimeSelector = $selectors['datetime'];
        $this->titleSelector = $selectors['title'];
        $this->imagesSelector = $selectors['images'];
        $this->textOverviewSelector = $selectors['textOverview'];
        $this->otherTextSelector = $selectors['otherText'];
    }

    /**
     * @return string
     */
    public function getNewsUrlSelector(): string
    {
        return $this->newsUrlSelector;
    }

    /**
     * @return string
     */
    public function getNewsContentSelector(): string
    {
        return $this->newsContentSelector;
    }

    /**
     * @return string
     */
    public function getCategorySelector(): string
    {
        return $this->categorySelector;
    }

    /**
     * @return string
     */
    public function getDatetimeSelector(): string
    {
        return $this->datetimeSelector;
    }

    /**
     * @return string
     */
    public function getTitleSelector(): string
    {
        return $this->titleSelector;
    }

    /**
     * @return string
     */
    public function getImagesSelector(): string
    {
        return $this->imagesSelector;
    }

    /**
     * @return string
     */
    public function getTextOverviewSelector(): string
    {
        return $this->textOverviewSelector;
    }

    /**
     * @return string
     */
    public function getOtherTextSelector(): string
    {
        return $this->otherTextSelector;
    }

    /**
     * @param array $selectors
     * @throws NotFoundException
     */
    public function validateSelectors(array $selectors): void
    {
        if (empty($selectors['newsContent'])) {
            throw new NotFoundException('Selector for find content of news is not found');
        }
        if (empty($selectors['category'])) {
            throw new NotFoundException('Selector for find category of news is not found');
        }
        if (empty($selectors['datetime'])) {
            throw new NotFoundException('Selector for find date and time of news is not found');
        }
        if (empty($selectors['title'])) {
            throw new NotFoundException('Selector for find title of news is not found');
        }
        if (empty($selectors['images'])) {
            throw new NotFoundException('Selector for find images of news is not found');
        }
        if (empty($selectors['newsUrl'])) {
            throw new NotFoundException('Selector for find urls of news is not found');
        }
        if (empty($selectors['otherText'])) {
            throw new NotFoundException('Selector for find all text of news is not found');
        }
        if (empty($selectors['textOverview'])) {
            throw new NotFoundException('Selector for text overview of news is not found');
        }
    }

    /**
     * Поиск ссылок на все новости на странице
     * @param string $html
     * @return array
     * @throws NotFoundException
     * @throws \Nette\Utils\JsonException
     */
    public function getNeedleUrlsFromHtml(string $html): array
    {
        $newsUrl = [];
        $dom = HtmlDomParser::str_get_html($html);
        $links = $dom->findMulti($this->newsUrlSelector);

        $counter = 0;
        foreach ($links as $link) {
            $newsUrl[] = $link->getAttribute('href');
            $counter++;
            if ($counter === NumberConstant::NEWS_TO_PARSE_AMOUNT) {
                break;
            }
        }

        return $newsUrl;
    }

    /**
     * Поиск всех нужных частей новости (категория, время, заголовок, текст, картинка) на ее странице
     * @param string $html
     * @return null|ArticleData
     */
    public function getDataFromNewsArticleHtml(string $html): ?ArticleData
    {
        $dom = HtmlDomParser::str_get_html($html);
        $articleHtml = $dom->findOneOrFalse($this->newsContentSelector);
        if ($articleHtml === false) {
            return null;
        }
        $articleHtml = str_replace("\n", '', $articleHtml->innerhtml());

        $dom = HtmlDomParser::str_get_html($articleHtml);
        $category = trim(strip_tags($dom->findOne($this->categorySelector)->innerHtml()));
        $articleTime = trim(strip_tags($dom->findOne($this->datetimeSelector)->getAttribute('content')));
        $articleTime = empty($articleTime) ? date('Y-m-d H:i:s') : $articleTime;
        $title = trim(strip_tags($dom->findOne($this->titleSelector)->innerHtml()));
        $images = $dom->findMultiOrFalse($this->imagesSelector);

        $imageSrc = null;
        $imageNames = [];
        if ($images !== false) {
            $counter = 0;
            foreach ($images as $image) {
                $imageSrc = $image->getAttribute('src');
                $imageContents = file_get_contents($imageSrc);
                $explodedSrc = explode('.', $imageSrc);
                $ext = array_pop($explodedSrc);
                unset($explodedSrc);
                $name = uniqid(sprintf('img%s_', ++$counter), true);
                $name = sprintf('%s.%s', $name, $ext);
                Storage::disk('public')->put($name, $imageContents);
                $imageNames[] = $name;
            }
        }

        $textOverview = $dom->findOneOrFalse($this->textOverviewSelector);
        if ($textOverview !== false) {
            $textOverview = sprintf('<p><b>%s</b></p>', trim(strip_tags($textOverview->innerHtml())));
        }

        $paragraphs = $dom->findMulti($this->otherTextSelector)->html;
        $article = $textOverview === false ? '' : $textOverview;
        foreach ($paragraphs as $paragraph) {
            $article = sprintf('%s%s', $article, $paragraph);
        }

        return new ArticleData(
            $article,
            $category,
            $articleTime,
            $title,
            $imageNames
        );
    }
}
