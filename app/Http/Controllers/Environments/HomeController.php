<?php

namespace App\Http\Controllers\Environments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct () {
        $this->middleware('guest');
    }

    protected function index ()
    {
        $carbon = Carbon::class;
        return view('layouts.home', compact(
            'carbon'
        ));
    }
}
