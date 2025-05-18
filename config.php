<?php
$host = 'localhost';
$dbname = 'viedece';
$user = 'root';
$pass = ''; // mot de passe vide sur XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
