<?php
// public function __construct(){
//         try {
//             $host = 'db1.ibu.edu.ba';
//             $username = 'webmakeup_24';
//             $password = 'web24makePWD';
//             $dbname = 'webmakeup';
//             $port = 3306;

//             $this->conn = new PDO(
//                 "mysql:host=" . $host . ";dbname=" . $dbname . ";port=" . $port,
//                 $username,
//                 $password, [
//                     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
//                 ]
//             );
//         } catch(PDOException $e) {
//             echo "Connection failed: " . $e->getMessage();
//         }
//     }

class ExamDao {

    private $conn;

    /**
     * constructor of dao class
     */
    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;port=3306;dbname=webfinal", "root", "");
            // Enable exception mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    /** TODO
     * Implement DAO method used to get customer information
     */
    public function get_customers() {
    $stmt = $this->conn->prepare("SELECT * FROM customers");  // Adjust table name if needed
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /** TODO
     * Implement DAO method used to get customer meals
     */
    public function get_customer_meals($customer_id) {
        $stmt = $this->conn->prepare("
        SELECT
            f.name AS food_name,
            f.brand AS food_brand,
            m.created_at AS meal_date
        FROM meals m
        JOIN foods f ON m.food_id = f.id
        WHERE m.customer_id = :customer_id
        ORDER BY m.created_at DESC
    ");
    $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    /** TODO
     * Implement DAO method used to save customer data
     */
    public function add_customer($data){
        $stmt = $this->conn->prepare("
        INSERT INTO customers (first_name, last_name, birth_date)
        VALUES (:first_name, :last_name, :birth_date)
    ");
    $stmt->bindParam(':first_name', $data['first_name']);
    $stmt->bindParam(':last_name', $data['last_name']);
    $stmt->bindParam(':birth_date', $data['birth_date']);
    $stmt->execute();

    // Return the inserted customer with ID
    $id = $this->conn->lastInsertId();

    return [
        'id' => $id,
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'birth_date' => $data['birth_date'],
    ];

    }

    /** TODO
     * Implement DAO method used to get foods report
     */
    public function get_foods_report($page = 1, $pageSize = 10) {

    $offset = ($page - 1) * $pageSize;

    $sql = "
        SELECT 
            f.id,
            f.name,
            f.brand,
            f.image_url,
            COALESCE(SUM(CASE WHEN fn.nutrient_id = 1 THEN fn.quantity END), 0) AS energy,
            COALESCE(SUM(CASE WHEN fn.nutrient_id = 2 THEN fn.quantity END), 0) AS protein,
            COALESCE(SUM(CASE WHEN fn.nutrient_id = 3 THEN fn.quantity END), 0) AS fat,
            COALESCE(SUM(CASE WHEN fn.nutrient_id = 4 THEN fn.quantity END), 0) AS fiber,
            COALESCE(SUM(CASE WHEN fn.nutrient_id = 5 THEN fn.quantity END), 0) AS carbs
        FROM foods f
        JOIN food_nutrients fn ON f.id = fn.food_id
        GROUP BY f.id
        LIMIT :offset, :limit
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$pageSize, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete_employee($employee_id) {
        $stmt = $this->conn->prepare("
        DELETE FROM employees WHERE employeeNumber = :employee_id");
    $stmt->execute([':employee_id' => $employee_id]);
    return ['message' => 'Employee deleted'];

    }


    public function update_employee($id, $data){
    $stmt = $this->conn->prepare("
        UPDATE employees
        SET firstName = :first_name,
            lastName = :last_name,
            email = :email
        WHERE employeeNumber = :id
    ");

    $stmt->bindParam(':first_name', $data['first_name']);
    $stmt->bindParam(':last_name', $data['last_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Return the updated employee
    $stmt = $this->conn->prepare("SELECT employeeNumber AS id, firstName, lastName, email FROM employees WHERE employeeNumber = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
     }

     public function login($data) {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['password'] === $data['password']) { // In production, use password_verify!
        return $user;
    }

    return false;
    }

    public function login1($email, $password){
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['password'] === $password) {
        return $user;
    }

    return false;
}
    
}
?>
