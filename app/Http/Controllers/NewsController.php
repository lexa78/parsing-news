<?php

namespace App\Http\Controllers;

use App\Models\NewsData;
use Illuminate\Contracts\View\View;

/**
 * Class NewsController
 * @package App\Http\Controllers
 */
class NewsController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $news = NewsData::orderBy('news_datetime')->get();

        return view('index', compact('news'));
    }

    /**
     * @param int $id
     * @return View
     */
    public function showWholeNews(int $id): View
    {
        $news = NewsData::where('id', $id)->first();

        return view('wholeNews', compact('news'));
    }

    /**
     * @param int $id
     * @return View
     */
    public function showAllNewsFromCategory(int $id): View
    {
        $news = NewsData::whereRelation('category', 'id', $id)->get();

        return view('index', compact('news'));
    }
}
