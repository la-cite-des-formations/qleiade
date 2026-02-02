<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (is_array($locale)) {
                \Illuminate\Support\Facades\Log::warning('Locale in session is an array', ['locale' => $locale]);
                $locale = is_string(head($locale)) ? head($locale) : config('app.locale');
            }
            App::setLocale($locale);
        }
        return $next($request);
    }
}
