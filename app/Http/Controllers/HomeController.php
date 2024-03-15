<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Table;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index() {
        $tables = Table::all();

        $data = [
            'tables' => $tables
        ];
        
        return Inertia::render('App')->with($data);    
    }

    public function createTable(Request $request) {
        DB::table('tables')->insert([
            'name' => $request->input('tableName'),
            'property' => $request->input('property'),
            'position_x' => $request->input('position_x'),
            'position_y' => $request->input('position_y')
        ]);
    }
}
