<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class HowToOrderController extends Controller
{
    public function index()
    {
        return Inertia::render('HowToOrder');
    }
}
