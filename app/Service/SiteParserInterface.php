<?php

namespace App\Service;

use App\ObjectValue\ArticleData;

/**
 * Interface SiteParserInterface
 * @package App\Service
 */
interface SiteParserInterface
{
    /**
     * @param string $html
     * @return array
     */
    public function getNeedleUrlsFromHtml(string $html): array;

    /**
     * @param string $html
     * @return null|ArticleData
     */
    public function getDataFromNewsArticleHtml(string $html): ?ArticleData;
}
