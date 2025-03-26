<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Assurez-vous que le chemin est correct

// Connexion à la base de données
$servername = "localhost";  // Remplace par ton hôte
$username = "root";         // Remplace par ton nom d'utilisateur de la base de données
$password = "";             // Remplace par ton mot de passe
$dbname = "devis_db"; // Remplace par ton nom de base de données

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST["name"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $project_type = htmlspecialchars($_POST["project-type"]);
    $budget = htmlspecialchars($_POST["budget"]);
    $deadline = htmlspecialchars($_POST["deadline"]);
    $message = nl2br(htmlspecialchars($_POST["message"]));

    if (!$email) {
        die(json_encode(["success" => false, "error" => "Email invalide."]));
    }

    // Insérer dans la base de données
    $sql = "INSERT INTO devis (nom, email, project_type, budget, deadline, message) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nom, $email, $project_type, $budget, $deadline, $message);

    if ($stmt->execute()) {
        // Envoyer l'email
        $mail = new PHPMailer(true);
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Remplace par ton serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'djorweboulga4@gmail.com'; // Remplace par ton email
            $mail->Password = 'ivojmwfzofyezbds'; // Utilise un mot de passe d'application si nécessaire
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Expéditeur et destinataire
            $mail->setFrom($email, $nom); // Expéditeur = email saisi dans le formulaire
            $mail->addAddress('djorweboulga4@gmail.com', 'Djorwe Boulga'); // Toi, le destinataire
            $mail->addReplyTo($email, $nom); // Permet de répondre directement à l'expéditeur

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = "Demande de devis de $nom";
            $mail->Body    = "
                <h3>Nouvelle demande de devis</h3>
                <p><strong>Nom :</strong> $nom</p>
                <p><strong>Email :</strong> $email</p>
                <p><strong>Type de projet :</strong> $project_type</p>
                <p><strong>Budget :</strong> $budget F CFA</p>
                <p><strong>Deadline :</strong> $deadline</p>
                <p><strong>Message :</strong><br>$message</p>
            ";

            // Envoi du mail
            if ($mail->send()) {
                ([header('Location: confirmation.html')]);
            } else {
                ([header('Location:error-message.html')]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => "Erreur dans l'envoi de l'email : " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Erreur dans l'insertion des données dans la base."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Méthode non autorisée."]);
}
?>