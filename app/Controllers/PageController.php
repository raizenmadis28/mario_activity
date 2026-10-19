<?php
namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller
{
    public function about(): void
    {
        $this->view('about', [
            'title' => 'About',
            'bg'    => '/Mario Wallpaper/NSMBUMushroomKingdom.png',
        ]);
    }

    public function contact(): void
    {
        $this->view('contact', [
            'title' => 'Contact',
            'bg'    => '/Mario Wallpaper/NSMBUMushroomKingdom.png',
        ]);
    }
}