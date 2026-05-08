<?php

require_once __DIR__ . '/../models/Resource.php';
require_once __DIR__ . '/../models/Category.php';

/**
 * Resource Controller
 */
class ResourceController
{
    private Resource $resourceModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->resourceModel = new Resource();
        $this->categoryModel = new Category();
    }

    /**
     * Handle resource search.
     *
     * @return void
     */
    public function search(): void
    {
        $query = $_GET['q'] ?? '';
        $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;

        $resources = $this->resourceModel->search($query, $categoryId);
        $categories = $this->categoryModel->findAll();

        require __DIR__ . '/../views/resources/search.php';
    }

    /**
     * Handle resource creation.
     *
     * @return void
     */
    public function create(): void
    {
        if (!isAuthenticated()) {
            flash('error', 'You must be logged in to add a resource.');
            redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'category_id' => $_POST['category_id'] ?? null,
                'format' => $_POST['format'] ?? '',
                'difficulty' => $_POST['difficulty'] ?? '',
                'description' => trim($_POST['description'] ?? ''),
                'created_by' => getCurrentUserId()
            ];

            // Validation
            if (empty($data['title']) || empty($data['category_id']) || empty($data['format']) || empty($data['difficulty'])) {
                flash('error', 'Please fill in all required fields.');
                redirect('/resources/create');
            }

            if (!in_array($data['format'], ['pdf', 'video', 'article'])) {
                flash('error', 'Invalid format selected.');
                redirect('/resources/create');
            }

            if (!in_array($data['difficulty'], ['easy', 'medium', 'hard'])) {
                flash('error', 'Invalid difficulty selected.');
                redirect('/resources/create');
            }

            if ($this->resourceModel->create($data)) {
                flash('success', 'Resource added successfully.');
                redirect('/resources/search');
            } else {
                flash('error', 'Failed to add resource.');
                redirect('/resources/create');
            }
        }

        $categories = $this->categoryModel->findAll();
        require __DIR__ . '/../views/resources/create.php';
    }
}
