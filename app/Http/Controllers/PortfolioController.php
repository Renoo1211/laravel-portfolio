<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PortfolioController extends Controller
{
    public function index(string $locale): View
    {
        app()->setLocale($locale);

        return view('portfolio', [
            'locale' => $locale,
            'otherLocale' => $locale === 'id' ? 'en' : 'id',
            'data' => config('portfolio'),
        ]);
    }
}
