<?php

require_once BASE_PATH . '/app/controllers/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index');
    }

    public function historyOrganization(): void
    {
        $this->view('home/history-organization');
    }

    public function departments(): void
    {
        $this->view('home/departments');
    }
    
    public function apartment(): void
    {
        require_once BASE_PATH . '/app/models/ApartmentType.php';
        $model = new ApartmentType();
        $apartmentTypes = $model->getTypesForUserView();
        
        $this->view('home/apartment', ['apartmentTypes' => $apartmentTypes]);
    }

    public function daawah(): void
    {
        $this->view('home/daawah');
    }

    public function damayan(): void
    {
        $this->view('home/damayan');
    }

    public function events(): void
    {
        $this->view('home/events');
    }

    public function announcements(): void
    {
        $this->view('home/announcements');
    }

    public function contact(): void
    {
        $this->view('home/contact');
    }
}
