<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // 👈 IMPORTANT

class Controller extends BaseController   // 👈 must extend BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}