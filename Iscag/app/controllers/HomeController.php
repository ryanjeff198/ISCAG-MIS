<?php

require_once BASE_PATH . '/app/controllers/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index');
    }
}
