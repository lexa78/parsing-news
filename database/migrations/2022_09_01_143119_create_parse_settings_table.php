<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Nette\Utils\Json;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parse_settings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('url');
            $table->json('mainPageParseOptions')->nullable();
            $table->json('options')->nullable();
            $table->json('selectors');
            $table->timestamps();
        });

        DB::table('parse_settings')->insert(
            [
                'code' => 'rbk',
                'url' => 'www.rbc.ru',
                'mainPageParseOptions' => Json::encode([
                    'headers' => [
                        'x-api-key' => env('PARSER_API_KEY'),
                    ]
                ]),
                'options' => Json::encode([
                    'headers' => [
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                        'Accept-Encoding' => 'gzip, deflate, br',
                        'Accept-Language' => 'ru-RU,ru;q=0.6',
                        'Cache-Control' => 'max-age=0',
                        'Connection' => 'keep-alive',
                        'Referer' => 'https://www.rbc.ru/',
                        'Sec-Fetch-Dest' => 'document',
                        'Sec-Fetch-Mode' => 'navigate',
                        'Sec-Fetch-Site' => 'same-origin',
                        'Sec-Fetch-User' => '?1',
                        'Sec-GPC' => '1',
                        'Upgrade-Insecure-Requests' => '1',
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36',
                    ]
                ]),
                'selectors' => Json::encode([
                    'newsUrl' => 'a.news-feed__item.js-visited.js-news-feed-item',
                    'newsContent' => 'div.l-col-center-590.article__content',
                    'category' => 'a.article__header__category',
                    'datetime' => 'time.article__header__date',
                    'title' => 'div.article__header__title h1.article__header__title-in',
                    'images' => 'div.article__main-image__wrap picture.smart-image img',
                    'textOverview' => 'div.article__text__overview span',
                    'otherText' => 'p',
                ]),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parse_settings');
    }
};
