<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require '../../vendor/autoload.php';                      // If using Composer
require './services/ExamService.php';                  // Your service

require './routes/ExamRoutes.php';                     // All routes

Flight::register('examService', 'ExamService');

// Flight::before('start', function (&$params, &$output) {
//     $protected_routes = [
//         'employees/performance'
        
//     ];

//     $request_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//     $request_method = $_SERVER['REQUEST_METHOD'];

//     foreach ($protected_routes as $protected) {
//         if (str_ends_with($request_url, $protected) && $request_method === 'GET') {
//             $headers = getallheaders();

//             if (!isset($headers['Authorization'])) {
//                 Flight::halt(401, json_encode(['message' => 'Missing Authorization header']));
//             }

//             $token = str_replace('Bearer ', '', $headers['Authorization']);

//             try {
//                 $decoded = JWT::decode($token, new Key('secret_key', 'HS256'));
//                 Flight::set('user', (array)$decoded); 
//             } catch (Exception $e) {
//                 Flight::halt(401, json_encode(['message' => 'Invalid or expired token']));
//             }
//         }
//     }
// });




Flight::before('start', function (&$params, &$output) {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        Flight::halt(401, json_encode(['message' => 'Missing authentication header']));
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);

    try {
        // Replace this line with your actual verification call if needed
        $decoded_token = JWT::decode($token, new Key('token', 'HS256'));

        // Store decoded user data in Flight for later use
        Flight::set('user', $decoded_token->user ?? null);
        Flight::set('jwt_token', $token);
    } catch (Exception $e) {
        Flight::halt(401, json_encode(['message' => 'Invalid or expired token']));
    }
});



Flight::start();
