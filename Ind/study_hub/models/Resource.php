<?php

require_once __DIR__ . '/../config/database.php';

/**
 * Resource Model
 */
class Resource
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Find the latest resources.
     *
     * @param int $limit
     * @return array
     */
    public function findLatest(int $limit = 3): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, c.name as category_name, u.username as author_name 
            FROM resources r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN users u ON r.created_by = u.id
            ORDER BY r.created_at DESC 
            LIMIT :limit
        ");
        // Bind limit specifically as integer for PostgreSQL limit
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Search resources by query and category.
     *
     * @param string $query
     * @param int|null $categoryId
     * @return array
     */
    public function search(string $query, ?int $categoryId = null): array
    {
        $sql = "
            SELECT r.*, c.name as category_name, u.username as author_name 
            FROM resources r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN users u ON r.created_by = u.id
            WHERE (r.title ILIKE :query OR r.description ILIKE :query)
        ";
        
        $params = ['query' => '%' . $query . '%'];

        if ($categoryId) {
            $sql .= " AND r.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $sql .= " ORDER BY r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Find all resources.
     *
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT r.*, c.name as category_name, u.username as author_name 
            FROM resources r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN users u ON r.created_by = u.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Find resource by ID.
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT r.*, c.name as category_name, u.username as author_name 
            FROM resources r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN users u ON r.created_by = u.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create a new resource.
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO resources (title, category_id, format, difficulty, description, created_by)
            VALUES (:title, :category_id, :format, :difficulty, :description, :created_by)
        ");
        return $stmt->execute([
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'format' => $data['format'],
            'difficulty' => $data['difficulty'],
            'description' => $data['description'],
            'created_by' => $data['created_by']
        ]);
    }

    /**
     * Update a resource.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE resources 
            SET title = :title, category_id = :category_id, format = :format, 
                difficulty = :difficulty, description = :description
            WHERE id = :id
        ");
        return $stmt->execute([
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'format' => $data['format'],
            'difficulty' => $data['difficulty'],
            'description' => $data['description'],
            'id' => $id
        ]);
    }

    /**
     * Delete a resource.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM resources WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
