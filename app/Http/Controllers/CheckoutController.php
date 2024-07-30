<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        if(empty(session('cart')))
        {
            return redirect(route('home.index'));
        }
        $design = config('app.design');
        return view($design . '.checkout');
    }

    public function checkout()
    {
        $design = config('app.design');
        $returnHTML = view($design . '.ajax.checkout_content')->with([
            'Language' => Language::class,
            'Currency' => Currency::class,
            'design' => $design,
        ])->render();
        return response()->json(array('success' => true, 'html'=>"$returnHTML"));
    }
}
