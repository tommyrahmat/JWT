<?php
//import script autoload agar bisa menggunakan library 
require_once ('./vendor/autoload.php');

//import library
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

//load custom environment variable nya
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//atur content type
header('Content-Type:application/json');

//cek method request
if ($_SERVER['REQUEST_METHOD'] !=='POST') {
    http_response_code(405);
    exit();
}

//ambil json yang dikirim oleh user
$json = file_get_contents('php://input');

//decode json tersebut agar mudah mengambil nilainya
$input_user = json_decode($json);

//jika tidak ada data email atau password
if (!isset($input_user->email) || !isset($input_user->password)) {
    http_response_code(400);
    exit();
}

$user = [
    'email' => 'tommy@example.com',
    'password' => 'tasik1234'
];

//jika email atau password tidak sesuai
if ($input_user->email !== $user['email'] || $input_user->password !== $user['password']) {
    echo json_encode ([
        'message' => 'Email atau password tidak sesuai'
    ]);
    exit();
}
//menghitung waktu kadaluarsa (terjadi selama 15 menit)
$waktu_kadaluarsa = time() + (15 * 60);

//buat payload dan access token
$payload = [
    'email' => $input_user->email,
    'exp' => $waktu_kadaluarsa
];

//generate access token
$access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET']);

// Kirim kembali ke user
echo json_encode([
  'success' => true,
  'data' => [
    'accessToken' => $access_token,
    'expiry' => date(DATE_ISO8601, $waktu_kadaluarsa)
  ],
  'message' => 'Login berhasil!'
]);

// Ubah waktu kadaluarsa lebih lama (terjadi selama 1 jam)
$payload['exp'] = time() + (60 * 60);
$refresh_token = JWT::encode($payload, $_ENV['REFRESH_TOKEN_SECRET']);

// Simpan refresh token di http-only cookie
setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);