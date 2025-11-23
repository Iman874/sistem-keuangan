<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\View;

class OwnerController extends Controller
{
    public function dashboard()
    {
    return View::make('owner.dashboard');
    }
}