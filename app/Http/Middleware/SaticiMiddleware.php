<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SaticiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $kullaniciRol = Session::get('kullanici_rol');
        
        if (!$kullaniciRol || ($kullaniciRol !== 'satici' && $kullaniciRol !== 'yonetici')) {
            return redirect()->route('home')->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}
