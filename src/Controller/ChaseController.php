<?php
namespace Locker\Controller;

use App\Controller\AppController;

class ChaseController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->autoRender = false;
    }


    public function index()
    {
        pr($this->Auth->role);
    }

    public function add()
    {
    }

}