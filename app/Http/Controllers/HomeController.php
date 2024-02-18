<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cat;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index() {
        return Inertia::render('App', []);    
    }
}
