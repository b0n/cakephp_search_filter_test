<?php

namespace App\Controller;

use Search\Controller\Component\PrgComponent;

/**
 * Topics Controller
 *
 * @mixin PrgComponent
 */
class TopicsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Search.Prg');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $query = $this->Topics->find('search',
            ['search' => $this->getRequest()->getQueryParams()]);
        $topics = $this->paginate($query);
        $this->set(compact('topics'));
    }

}
