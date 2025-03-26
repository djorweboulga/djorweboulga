<?php
header("Content-Type: application/json");

// Connexion à la base de données
$host = "localhost";
$user = "root"; // Remplace par ton user MySQL
$pass = ""; // Remplace par ton mot de passe MySQL
$dbname = "devis_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connexion échouée: " . $conn->connect_error]));
}

// Récupération des données POST
$nom = $_POST['name'];
$email = $_POST['email'];
$project_type = $_POST['project-type'];
$budget = $_POST['budget'];
$deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : NULL;
$message = $_POST['message'];

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "L'email fourni n'est pas valide."]);
    exit;
}

// Insertion dans la base de données
$sql = "INSERT INTO devis (nom, email, project_type, budget, deadline, message) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssiss", $nom, $email, $project_type, $budget, $deadline, $message);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Erreur lors de l'enregistrement."]);
}

$stmt->close();
$conn->close();
?>