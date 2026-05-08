<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Resource.php';
require_once __DIR__ . '/../models/Category.php';

/**
 * Admin Controller
 */
class AdminController
{
    private User $userModel;
    private Resource $resourceModel;
    private Category $categoryModel;

    public function __construct()
    {
        if (!isAdmin()) {
            flash('error', 'Access denied. Admins only.');
            redirect('/');
        }
        $this->userModel = new User();
        $this->resourceModel = new Resource();
        $this->categoryModel = new Category();
    }

    /**
     * Manage users (list and assign roles).
     *
     * @return void
     */
    public function users(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'make_admin') {
            $userId = (int)$_POST['user_id'];
            if ($this->userModel->updateRole($userId, 'admin')) {
                flash('success', 'User promoted to admin.');
            } else {
                flash('error', 'Failed to promote user.');
            }
            redirect('/admin/users');
        }

        $users = $this->userModel->findAll();
        require __DIR__ . '/../views/admin/users.php';
    }

    /**
     * Manage resources (list, edit, delete).
     *
     * @return void
     */
    public function resources(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'delete') {
                $resourceId = (int)$_POST['resource_id'];
                if ($this->resourceModel->delete($resourceId)) {
                    flash('success', 'Resource deleted.');
                } else {
                    flash('error', 'Failed to delete resource.');
                }
                redirect('/admin/resources');
            }
            
            if ($_POST['action'] === 'edit') {
                $resourceId = (int)$_POST['resource_id'];
                $data = [
                    'title' => trim($_POST['title'] ?? ''),
                    'category_id' => $_POST['category_id'] ?? null,
                    'format' => $_POST['format'] ?? '',
                    'difficulty' => $_POST['difficulty'] ?? '',
                    'description' => trim($_POST['description'] ?? '')
                ];
                
                if ($this->resourceModel->update($resourceId, $data)) {
                    flash('success', 'Resource updated.');
                } else {
                    flash('error', 'Failed to update resource.');
                }
                redirect('/admin/resources');
            }
        }

        $resources = $this->resourceModel->findAll();
        $categories = $this->categoryModel->findAll();
        require __DIR__ . '/../views/admin/resources.php';
    }

    /**
     * Manage categories (list, add, delete).
     *
     * @return void
     */
    public function categories(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $name = trim($_POST['name'] ?? '');
                if (!empty($name) && $this->categoryModel->create($name)) {
                    flash('success', 'Category added.');
                } else {
                    flash('error', 'Failed to add category. It may already exist.');
                }
            } elseif ($_POST['action'] === 'delete') {
                $categoryId = (int)$_POST['category_id'];
                if ($this->categoryModel->delete($categoryId)) {
                    flash('success', 'Category deleted.');
                } else {
                    flash('error', 'Failed to delete category.');
                }
            }
            redirect('/admin/categories');
        }

        $categories = $this->categoryModel->findAll();
        require __DIR__ . '/../views/admin/categories.php';
    }
}
