<?php

require_once __DIR__ . '/../models/Resource.php';

/**
 * Home Controller
 */
class HomeController
{
    private Resource $resourceModel;

    public function __construct()
    {
        $this->resourceModel = new Resource();
    }

    /**
     * Display the home page with latest resources.
     *
     * @return void
     */
    public function index(): void
    {
        $latestResources = $this->resourceModel->findLatest(3);
        require __DIR__ . '/../views/home.php';
    }
}
