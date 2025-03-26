<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Charge PHPMailer

session_start();

// Vérification des données du formulaire et envoi d'email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $mail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL); // Nettoyage de l'email
    $objet = htmlspecialchars($_POST['objet']);
    $message = htmlspecialchars($_POST['message']);

    // Vérification que l'email est valide
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        exit('<h3 align="center">Adresse email invalide - <a href="javascript:history.back();">Retour au formulaire</a></h3>');
    }

    // Configuration de PHPMailer
    $mailSender = new PHPMailer(true);
    try {
        // Paramètres SMTP
        $mailSender->isSMTP();
        $mailSender->Host       = 'smtp.gmail.com';
        $mailSender->SMTPAuth   = true;
        $mailSender->Username   = 'djorweboulga4@gmail.com'; // Ton email
        $mailSender->Password   = 'ivojmwfzofyezbds'; // Ton mot de passe d'application
        $mailSender->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailSender->Port       = 587;

        // Expéditeur et destinataire
        $mailSender->setFrom('djorweboulga4@gmail.com', 'Formulaire de Contact'); // Ton email comme expéditeur officiel
        $mailSender->addAddress('djorweboulga4@gmail.com'); // Ton email où tu reçois les messages
        $mailSender->addReplyTo($mail, $nom); // Permet de répondre directement à l'expéditeur

        // Contenu de l'email
        $mailSender->isHTML(true);
        $mailSender->Subject = $objet;
        $mailSender->Body    = "<p><strong>Nom :</strong> $nom</p>
                                <p><strong>Email :</strong> $mail</p>
                                <p><strong>Message :</strong><br>" . nl2br($message) . "</p>";
        $mailSender->AltBody = "Nom: $nom\nEmail: $mail\nMessage:\n$message"; // Version texte brut

        // Envoi de l'email
        $mailSender->send();

        header('Location: confirmation.html'); // Page de confirmation
        exit;
    } catch (Exception $e) {
        echo '<h3 align="center">Erreur lors de l\'envoi de l\'email : ' . $mailSender->ErrorInfo . '</h3>';
        exit;
    }
} else {
    exit('<h3 align="center">Aucune donnée soumise - <a href="javascript:history.back();">Retour au formulaire</a></h3>');
}
?>
