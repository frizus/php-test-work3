<?php

namespace App\Http\Controllers;

class HomeController
{
    public function home(): string
    {
        return view('home');
    }
}
