<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 

require 'vendor/autoload.php'; // Assure-toi que PHPMailer est installé via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "djorweboulga4@gmail.com"; // Remplace par ton adresse email
    $subject = "Nouveau message depuis ton site web";
    $nom = htmlspecialchars($_POST["name"]);
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $project_type = htmlspecialchars($_POST["project-type"]);
    $budget = htmlspecialchars($_POST["budget"]);
    $deadline = htmlspecialchars($_POST["deadline"]);
    $message = nl2br(htmlspecialchars($_POST["message"]));
    
    // Crée un nouvel objet PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // SMTP de Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'djorweboulga4@gmail.com'; // Remplace par ton email
        $mail->Password   = 'ivojmwfzofyezbds'; // Remplace par ton mot de passe ou mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // De et pour l'email
        $mail->setFrom('djorweboulga4@gmail.com', 'DJORWE BOULGA'); // Remplace par ton email et ton nom
        $mail->addAddress($to); // Destinataire

        // Ajoute l'email de réponse à partir du formulaire
        $replyTo = $_POST['email'] ?? 'no-reply@tonsite.com';
        $mail->addReplyTo($replyTo);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        $message =  "
        <h3>Nouvelle demande de devis</h3>
        <p><strong>Nom :</strong> $nom</p>
        <p><strong>Email :</strong> $email</p>
        <p><strong>Type de projet :</strong> $project_type</p>
        <p><strong>Budget :</strong> $budget F CFA</p>
        <p><strong>Deadline :</strong> $deadline</p>
        <p><strong>Message :</strong><br>$message</p>
    ";
        
        if (isset($_POST['form_type']) && $_POST['form_type'] == "contact") {
            $message .= "<h2>Formulaire de Contact</h2>";
            $message .= "<p><strong>Nom:</strong> " . htmlspecialchars($_POST['nom']) . "</p>";
            $message .= "<p><strong>Email:</strong> " . htmlspecialchars($_POST['email']) . "</p>";
            $message .= "<p><strong>Message:</strong> " . nl2br(htmlspecialchars($_POST['message'])) . "</p>";
        } elseif (isset($_POST['form_type']) && $_POST['form_type'] == "devis") {
            $message .= "<h2>Demande de Devis</h2>";
            $message .= "<p><strong>Nom:</strong> " . htmlspecialchars($_POST['nom']) . "</p>";
            $message .= "<p><strong>Email:</strong> " . htmlspecialchars($_POST['email']) . "</p>";

            // Vérification et récupération des champs de devis
            if (isset($_POST['service']) && !empty($_POST['service'])) {
                $message .= "<p><strong>Service demandé:</strong> " . htmlspecialchars($_POST['service']) . "</p>";
            } else {
                $message .= "<p><strong>Service demandé:</strong> Non précisé</p>";
            }
            
            if (isset($_POST['description']) && !empty($_POST['description'])) {
                $message .= "<p><strong>Description:</strong> " . nl2br(htmlspecialchars($_POST['description'])) . "</p>";
            } else {
                $message .= "<p><strong>Description:</strong> Non précisée</p>";
            }
        }
        
        $message .= "</body></html>";

        // Ajoute le contenu HTML à l'email
        $mail->Body = $message;

        // Envoi de l'email
        if ($mail->send()) {
            header('Location: confirmation.html'); // Page de confirmation
            exit;
        } else {
            exit('<h3 align="center">Aucune donnée soumise - <a href="javascript:history.back();">Retour au formulaire</a></h3>');
        }
    } catch (Exception $e) {
        echo '<h3 align="center">Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo . '</h3>';
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Méthode non autorisée."]);
}
?>