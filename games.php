<?php
// import script autoload agar bisa menggunakan library
require_once('./vendor/autoload.php');

// import library
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

// load custom environment variable nya
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// atur content type
header('Content-Type: application/json');

// cek method request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405);
  exit();
}

$headers = getallheaders();

// periksa apakah header authorization-nya ada
if (!isset($headers['Authorization'])) {
  http_response_code(401);
  exit();
}

// mengambil token
list(, $token) = explode(' ', $headers['Authorization']);

try {
  // men-decode token sekaligus memverfikasinya
  JWT::decode($token, $_ENV['ACCESS_TOKEN_SECRET'], ['HS256']);
  
  $games = [
    [
      'title' => 'Valorant',
      'genre' => 'Strategy'
    ],
    [
      'title' => 'Mario Bros',
      'genre' => 'Adventure'
    ]
  ];


  echo json_encode($games);
} catch (Exception $e) {
  // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
  http_response_code(401);
  exit();
}