<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::route('GET /connection-check', function(){
    /** TODO
    * This endpoint prints the message from constructor within ExamDao class
    * Goal is to check whether connection is successfully established or not
    * This endpoint does not have to return output in JSON format
    * 5 points
    */
    try{
        new ExamDao();
    }catch(Exception $e){
        echo "A problem occured: " . $e;
    }
});

Flight::route('GET /customers', function(){
    /** TODO
    * This endpoint returns list of all customers that will be used
    * to populate the <select> list
    * This endpoint should return output in JSON format
    * 10 points
    */
    $customers = Flight::examService()->get_customers();
    Flight::json($customers);
});

Flight::route('GET /customer/meals/@customer_id', function($customer_id){
    /** TODO
    * This endpoint returns array of all meals for a specific customer
    * Every item in the array should have following properties
    *   `food_name` -> name of the food that customer eat for the meal
    *   `food_brand` -> brand of the food that customer eat for the meal
    *   `meal_date` -> date when the customer eat the meal
    * This endpoint should return output in JSON format
    * 10 points
    */
    $meals = Flight::examService()->get_customer_meals($customer_id);
    Flight::json($meals);
});

Flight::route('POST /customers/add', function() {
    /** TODO
    * This endpoint should add the customer to the database
    * The data that will come from the form (if you don't change
    * the template form) has following properties
    *   `first_name` -> first name of the customer
    *   `last_name` -> last name of the customer
    *   `birth_date` -> date when the customer has been born
    * This endpoint should return the added customer in JSON format
    * 10 points
    */
    $payload = Flight::request()->data->getData();

    if (!empty($payload['first_name']) && !empty($payload['last_name']) && !empty($payload['birth_date'])) {
        $added_customer = Flight::examService()->add_customer($payload);

        Flight::json([
            'status' => 'success',
            'customer' => $added_customer
        ]);
    } else {
        Flight::json([
            'status' => 'error',
            'message' => 'Missing required fields'
        ], 400);
    }

});

Flight::route('GET /foods/report', function(){
    /** TODO
    * This endpoint should return the array of all foods from the database
    * together with the image of the foods. This endpoint should be fully
    * paginated. Every food returned should have following properties:
    *   `name` -> name of the food
    *   `brand` -> brand of the food
    *   `image` -> <img> of the food
    *   `energy` -> total amount of calories (energy) of the food
    *   `protein` -> total amount of proteins of the food
    *   `fat` -> total amount of fat of the food
    *   `fiber` -> total amount of fiber of the food
    *   `carbs` -> total amount of carbs of the food
    * This endpoint should return output in JSON format
    * 15 points
    */
    $page = Flight::request()->query['page'] ?? 1;
$pageSize = Flight::request()->query['pageSize'] ?? 10;

$foods = Flight::examService()->foods_report((int)$page, (int)$pageSize);

Flight::json($foods);
});

Flight::route('DELETE /employee/delete/@employee_id', function($employee_id){
    /** TODO
    * This endpoint should delete the employee from database with provided id.
    * This endpoint should return output in JSON format that contains only 
    * `message` property that indicates that process went successfully.
    * 5 points
    */
    $employee = Flight::examService()->delete_employee($employee_id);
    Flight::json($employee);

    
    
});

Flight::route('PUT /employees/@id', function($id){
    $payload = Flight::request()->data->getData();
    $employee = Flight::examService()->update_employee($id, $payload);
    Flight::json($employee);
});

// {
//   "first_name": "Jane",
//   "last_name": "Doe",
//   "email": "jane.doe@example.com"
// }

Flight::route('POST /login', function () {
    $data = Flight::request()->data->getData();
    $user = Flight::examService()->login($data);

    if ($user) {
        $jwt = base64_encode(json_encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'exp' => time() + 3600
        ]));

        Flight::json([
            'message' => 'Login successful',
            'token' => $jwt,
            'user' => $user
        ]);
    } else {
        Flight::json([
            'message' => 'Invalid email or password'
        ], 401);
    }
});

// {
//     "email":"demo.user@gmail.com",
//     "password":"123"
// }

Flight::route('GET /final/login1', function(){
    /** TODO
    * This endpoint is used to login user to system
    * you can use email: demo.user@gmail.com and password: 123 to login
    * Output should be array containing success message and JWT for this user
    * Sample output is given in figure 7
    * This endpoint should return output in JSON format
    */
    $email = Flight::request()->query['email'] ?? '';
    $password = Flight::request()->query['password'] ?? '';

    $user = Flight::examService()->login1($email, $password);

    if ($user) {
        $key = "your_secret_key"; 
        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'exp' => time() + 3600
        ];
        $jwt = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');

        Flight::json([
            "message" => "Login successful",
            "token" => $jwt
        ]);
    } else {
        Flight::json([
            "message" => "Invalid credentials"
        ], 401);
    }
// http://localhost/final-2025-fall-odgovori/backend/rest/final/login1?email=demo.user@gmail.com&password=123

});

?>
