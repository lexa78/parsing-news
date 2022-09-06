<?php

namespace App\ObjectValue;

/**
 * Class ArticleData
 * @package App\ObjectValue
 */
class ArticleData
{
    /** @var string  */
    private string $category;
    /** @var string  */
    private string $articleDatetime;
    /** @var string  */
    private string $title;
    /** @var array  */
    private array $imageNames;
    /** @var string  */
    private string $article;

    /**
     * ArticleData constructor.
     * @param string $article
     * @param string $category
     * @param string $articleDatetime
     * @param string $title
     * @param array $imageNames
     */
    public function __construct(
        string $article,
        string $category = '',
        string $articleDatetime = '',
        string $title = '',
        array $imageNames = []
    ) {
        $this->article = $article;
        $this->category = $category;
        $this->articleDatetime = $articleDatetime;
        $this->title = $title;
        $this->imageNames = $imageNames;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory(string $category): ArticleData
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getArticleDatetime(): string
    {
        return $this->articleDatetime;
    }

    /**
     * @param string $articleDatetime
     * @return $this
     */
    public function setArticleDatetime(string $articleDatetime): ArticleData
    {
        $this->articleDatetime = $articleDatetime;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): ArticleData
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->imageNames;
    }

    /**
     * @param array $images
     * @return $this
     */
    public function setImages(array $images): ArticleData
    {
        $this->imageNames = $images;

        return $this;
    }

    /**
     * @return string
     */
    public function getArticle(): string
    {
        return $this->article;
    }

    /**
     * @param string $article
     * @return $this
     */
    public function setArticle(string $article): ArticleData
    {
        $this->article = $article;

        return $this;
    }
}
