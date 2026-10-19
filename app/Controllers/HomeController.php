<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

class HomeController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $common = [
            'title' => 'Dashboard',
            'bg'    => '/Mario Wallpaper/NSMBUMushroomKingdom.png',
        ];

        if (Auth::isAdmin()) {
            $this->view('home_admin', $common);
        } else {
            $this->view('home_customer', $common);
        }
    }
}