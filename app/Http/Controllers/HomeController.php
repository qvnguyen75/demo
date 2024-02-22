<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Entity;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    
    public function index() {
        $entity = Entity::find(1);
        $data = [
            'entity' => $entity
        ];
        return Inertia::render('App')->with($data);    
    }
}
