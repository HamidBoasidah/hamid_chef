<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetLocaleRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class LocaleController extends Controller
{
    public function __invoke(SetLocaleRequest $request): Response
    {
        $locale = $request->locale();

        // Session + (اختياري) قاعدة بيانات + Cookie
        session(['locale' => $locale]);
        if ($request->user() && Schema::hasColumn('users', 'locale')) {
            $request->user()->forceFill(['locale' => $locale])->save();
        }
        
        return response()->noContent(204)->cookie(
            'locale', $locale, 60*24*365, '/', null, false, false, false, 'lax'
        );
    }
}