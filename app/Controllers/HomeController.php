<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

class HomeController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $this->view('home', [
            'title' => 'Dashboard',
            'bg'    => '/Mario Wallpaper/NSMBUMushroomKingdom.png',
        ]);
    }
}