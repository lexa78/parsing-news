<?php

namespace App\Console\Commands;

use App\Exceptions\NotFoundException;
use App\Models\Category;
use App\Models\Image;
use App\Models\NewsData;
use App\Models\ParseSetting;
use App\Constant\Number as NumberConstant;
use App\Service\RemoteDataReceiver;
use App\Service\SiteParserInterface;
use Illuminate\Console\Command;
use Nette\Utils\Json;

use function count;
use function is_null;
use function sprintf;
use function strtolower;
use function ucfirst;
use function trim;
use function class_exists;
use function in_array;
use function array_search;

/**
 * Class ParseNews
 * @package App\Console\Commands
 */
class ParseNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse news from needle site';

    /**
     * ParseNews constructor.
     * @throws NotFoundException
     */
    public function __construct()
    {
        if (is_null(env('DEFAULT_SITE_CODE_FOR_NEWS_PARSE'))) {
            throw new NotFoundException(
                'Value of parameter DEFAULT_SITE_CODE_FOR_NEWS_PARSE in .env file is not found'
            );
        }
        $this->signature = sprintf(
            '%s {siteCode=%s : code of site which need to parse from DB}',
            $this->signature,
            env('DEFAULT_SITE_CODE_FOR_NEWS_PARSE')
        );
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param RemoteDataReceiver $remoteDataReceiver
     * @return int
     * @throws \App\Exceptions\BadHttpResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(RemoteDataReceiver $remoteDataReceiver)
    {
        $siteCode = trim($this->argument('siteCode'));
        $parseSettings = ParseSetting::where('code', $siteCode)->first();

        if ($parseSettings instanceof ParseSetting) {
            $html = $remoteDataReceiver->getPageHtml(
                $parseSettings->url,
                empty($parseSettings->options) ? [] : Json::decode($parseSettings->options, true)
            );
        } else {
            throw new NotFoundException(sprintf('Settings by code %s are not found', $siteCode));
        }

        if (empty($parseSettings->selectors)) {
            throw new NotFoundException(sprintf('Selectors to parse new for code %s are not found', $siteCode));
        }

        /** Я не придумал, как сделать dependency injection в зависимости от аргумента, переданного
         * в команде, по-этом сделал так */
        $needleClass = sprintf('\App\Service\%sParser', ucfirst(strtolower($siteCode)));
        if (!class_exists($needleClass)) {
            throw new NotFoundException(sprintf('Class %s is not found', $needleClass));
        }

        /** @var SiteParserInterface $pageParser */
        $pageParser = new $needleClass(Json::decode($parseSettings->selectors, true));
        $urls = $pageParser->getNeedleUrlsFromHtml($html);
        if (count($urls) === 0) {
            throw new NotFoundException('No links on news found on this page');
        }

        $counter = 0;
        $categories = Category::getCategoriesAsArrayWithIdAsKey();
        foreach ($urls as $url) {
            if ($counter === NumberConstant::NEWS_TO_PARSE_AMOUNT) {
                break;
            }

            $html = $remoteDataReceiver->getPageHtml(
                $url,
                empty($parseSettings->options) ? [] : Json::decode($parseSettings->options, true)
            );

            $articleData = $pageParser->getDataFromNewsArticleHtml($html);
            if (is_null($articleData)) {
                continue;
            }

            $newsModel = new NewsData();
            if (in_array($articleData->getCategory(), $categories)) {
                $categoryId = array_search($articleData->getCategory(), $categories);
            } else {
                $categoryModel = Category::create([
                    'name' => $articleData->getCategory(),
                ]);
                $categoryId = $categoryModel->id;
                $categories[$categoryId] = $articleData->getCategory();
                unset($categoryModel);
            }
            $newsModel->title = $articleData->getTitle();
            $newsModel->news_text = $articleData->getArticle();
            $newsModel->news_datetime = $articleData->getArticleDatetime();
            $newsModel->category_id = $categoryId;
            $newsModel->save();

            foreach ($articleData->getImages() as $imageName) {
                $imageModel = new Image();
                $imageModel->name = $imageName;
                $imageModel->news_id = $newsModel->id;
                $imageModel->save();
            }

            $counter++;
            $this->info(sprintf('News number %s saved successfully', $counter));
        }

        $this->info(sprintf('There are %s news was saved', $counter));
        return 0;

    }
}
