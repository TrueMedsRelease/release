<?php

namespace App\Http\Controllers;


use Illuminate\View\View;
use Phattarachai\LaravelMobileDetect\Agent;
use App\Services\ProductServices;

class AdminController extends Controller
{
    public function admin_login() : View
    {
        $design = session('design') ? session('design') : config('app.design');
        $title = ProductServices::getPageTitle('index');
        $agent = new Agent();

        return view('admin.login',
        [
            'design' => $design,
            'title' => $title,
            'agent' => $agent,
        ]);
    }
}