<?php
require_once 'Database.class.php';

class Account
{
    private $db;
    public $id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $age;
    public $address;
    public $email;
    public $role;
    public $department;
    public $password;
    public $contact_number;
    public $username;
    public $profile_image;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function fetchAccounts()
    {
        $sql = "SELECT users.*, role.name AS role, department.name AS department FROM users INNER JOIN role ON users.role_id = role.id INNER JOIN department ON users.department_id = department.id";
        $result = $this->db->connect()->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchRoles()
    {
        $sql = "SELECT * FROM role";
        $result = $this->db->connect()->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchDepartments()
    {
        $sql = "SELECT * FROM department";
        $result = $this->db->connect()->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function uploadProfileImage($file)
    {
        if (empty($file) || empty($file['name'])) {
            return '../assets/default-profile.png';
        }

        $target_dir = "uploads/profile_images/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File is not an image.");
        }

        if ($file["size"] > 5000000) {
            throw new Exception("Sorry, your file is too large. Maximum size is 5MB.");
        }

        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($file_extension, $allowed_types)) {
            throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    }

    public function add()
    {
        $required_fields = ['first_name', 'middle_name', 'last_name', 'age', 'address', 'email', 'role', 'department', 'password', 'contact_number', 'username'];
        foreach ($required_fields as $field) {
            if (empty($this->$field)) {
                throw new Exception("Required field '$field' is missing or empty.");
            }
        }

        try {
            $profile_image = isset($_FILES['profile_image']) ? $this->uploadProfileImage($_FILES['profile_image']) : 'assets/default-profile.png';

            $sql = "INSERT INTO users (first_name, middle_name, last_name, age, address, email, role_id, department_id, password, contact_number, username, profile_image) VALUES (:first_name, :middle_name, :last_name, :age, :address, :email, :role, :department, :password, :contact_number, :username, :profile_image)";
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':middle_name', $this->middle_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':age', $this->age);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':role', $this->role);
            $stmt->bindParam(':department', $this->department);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':contact_number', $this->contact_number);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':profile_image', $profile_image);

            if ($stmt->execute()) {
                $this->id = $this->db->connect()->lastInsertId();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new Exception("Error adding account: " . $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $profile_image = isset($_FILES['profile_image']) && !empty($_FILES['profile_image']['name'])
                ? $this->uploadProfileImage($_FILES['profile_image'])
                : $this->profile_image;

            $sql = "UPDATE users SET 
                    first_name = :first_name, 
                    middle_name = :middle_name, 
                    last_name = :last_name, 
                    age = :age, 
                    address = :address, 
                    email = :email, 
                    role_id = :role, 
                    department_id = :department, 
                    password = :password, 
                    contact_number = :contact_number, 
                    username = :username, 
                    profile_image = :profile_image 
                    WHERE id = :id";

            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':middle_name', $this->middle_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':age', $this->age);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':role', $this->role);
            $stmt->bindParam(':department', $this->department);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':contact_number', $this->contact_number);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':profile_image', $profile_image);
            $stmt->bindParam(':id', $this->id);

            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Error updating account: " . $e->getMessage());
        }
    }

    public function delete($user_id)
    {
        try {
            $this->db->connect()->beginTransaction();

            $delete_borrowings_sql = "DELETE FROM borrowings WHERE user_id = (SELECT id FROM users WHERE id = ?)";
            $delete_borrowings_stmt = $this->db->connect()->prepare($delete_borrowings_sql);
            $delete_borrowings_stmt->execute([$user_id]);

            $delete_user_sql = "DELETE FROM users WHERE id = ?";
            $delete_user_stmt = $this->db->connect()->prepare($delete_user_sql);
            $delete_user_stmt->execute([$user_id]);

            $this->db->connect()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->connect()->rollback();
            throw new Exception("Error deleting account: " . $e->getMessage());
        }
    }

    public function fetchRecord($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
