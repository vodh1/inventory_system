<?php
require_once 'database.class.php';

class Equipment
{
    private $db;
    public $id;
    public $name;
    public $description;
    public $category_id;
    public $max_borrow_days;
    public $units;
    public $image_path;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function add()
    {
        $sql = "INSERT INTO equipment (name, description, category_id, max_borrow_days, image_path) VALUES (:name, :description, :category_id, :max_borrow_days, :image_path)";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':max_borrow_days', $this->max_borrow_days);
        $stmt->bindParam(':image_path', $this->image_path);

        if ($stmt->execute()) {
            $this->id = $this->db->connect()->lastInsertId();
            $this->addUnits($this->units);
            return true;
        }
        return false;
    }

    public function addUnits($units)
    {
        $sql = "INSERT INTO equipment_units (equipment_id, unit_code, status) VALUES (:equipment_id, :unit_code, 'available')";
        $stmt = $this->db->connect()->prepare($sql);

        for ($i = 1; $i <= $units; $i++) {
            $unit_code = sprintf('UNIT-%03d-%03d', $this->id, $i);
            $stmt->bindValue(':equipment_id', $this->id);
            $stmt->bindValue(':unit_code', $unit_code);
            $stmt->execute();
        }
    }

    public function fetchLastAdded()
    {
        $sql = "SELECT e.*, c.name AS category_name, 
                (SELECT COUNT(*) FROM equipment_units WHERE equipment_id = e.id AND status = 'available') as available_units,
                (SELECT COUNT(*) FROM equipment_units WHERE equipment_id = e.id) as total_units
                FROM equipment e
                JOIN categories c ON e.category_id = c.id
                WHERE e.id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function showAll($category = '', $search = '', $offset = 0, $limit = 10)
    {
        $sql = "SELECT e.*, c.name AS category_name, 
                (SELECT COUNT(*) FROM equipment_units WHERE equipment_id = e.id AND status = 'available') as available_units,
                (SELECT COUNT(*) FROM equipment_units WHERE equipment_id = e.id) as total_units
                FROM equipment e
                JOIN categories c ON e.category_id = c.id";

        $where_conditions = [];

        if (!empty($category)) {
            $where_conditions[] = "e.category_id = :category";
        }
        if (!empty($search)) {
            $where_conditions[] = "(e.name LIKE :search OR c.name LIKE :search)";
        }

        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(' AND ', $where_conditions);
        }

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->connect()->prepare($sql);
        if (!empty($category)) $stmt->bindParam(':category', $category);
        if (!empty($search)) $stmt->bindValue(':search', '%' . $search . '%');
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchCategory()
    {
        $sql = "SELECT * FROM categories";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchName($name)
    {
        $sql = "SELECT * FROM equipment WHERE LOWER(name) = LOWER(:name)";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchById($id)
    {
        $sql = "SELECT * FROM equipment WHERE id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchRecord($id)
    {
        $sql = "SELECT e.*, c.name AS category_name, 
                (SELECT COUNT(*) FROM equipment_units WHERE equipment_id = e.id AND status = 'available') as available_units,
                (SELECT COUNT(*) FROM equipment_units WHERE equipment_id = e.id) as total_units
                FROM equipment e
                JOIN categories c ON e.category_id = c.id
                WHERE e.id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function edit()
    {
        if ($this->image_path) {
            $sql = "UPDATE equipment SET name = :name, description = :description, category_id = :category_id, max_borrow_days = :max_borrow_days, image_path = :image_path WHERE id = :id";
        } else {
            $sql = "UPDATE equipment SET name = :name, description = :description, category_id = :category_id, max_borrow_days = :max_borrow_days WHERE id = :id";
        }
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':max_borrow_days', $this->max_borrow_days);
        $stmt->bindParam(':id', $this->id);
        if ($this->image_path) {
            $stmt->bindParam(':image_path', $this->image_path);
        }

        if ($stmt->execute()) {
            // Update units if specified
            if (isset($this->units)) {
                $this->updateUnits($this->units);
            }
            return true;
        }
        return false;
    }

    public function updateUnits($newUnits)
    {
        // Get current number of units
        $sql = "SELECT COUNT(*) as current_units FROM equipment_units WHERE equipment_id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentUnits = $result['current_units'];

        // If new units are more than current, add new units
        if ($newUnits > $currentUnits) {
            $sql = "INSERT INTO equipment_units (equipment_id, unit_code, status) VALUES (:equipment_id, :unit_code, 'available')";
            $stmt = $this->db->connect()->prepare($sql);

            for ($i = $currentUnits + 1; $i <= $newUnits; $i++) {
                $unit_code = sprintf('UNIT-%03d-%03d', $this->id, $i);
                $stmt->bindValue(':equipment_id', $this->id);
                $stmt->bindValue(':unit_code', $unit_code);
                $stmt->execute();
            }
        }
        // If new units are less than current, remove excess units (only if no units are borrowed)
        elseif ($newUnits < $currentUnits) {
            $sql = "DELETE FROM equipment_units 
                    WHERE equipment_id = :id AND status = 'available' 
                    ORDER BY id DESC
                    LIMIT :limit";
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindValue(':id', $this->id);
            $stmt->bindValue(':limit', $currentUnits - $newUnits, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function delete($id)
    {
        // First, delete related records in equipment_units table
        $sql = "DELETE FROM equipment_units WHERE equipment_id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Then, delete the equipment record
        $sql = "DELETE FROM equipment WHERE id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
