<?php
function h($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$errors = [];
$resultatEcriture = null;
$mailEnvoye = false;

if ($isPost) {
    $nom = h($_POST['nom'] ?? '');
    $email = h($_POST['email'] ?? '');
    $message = h($_POST['message'] ?? '');

    if ($nom === '' || $email === '' || $message === '') {
        $errors[] = "Tous les champs sont obligatoires.";
    } else {
        $dateHeure = date('Y-m-d H:i:s');
        $ligne = "[$dateHeure] Nom : $nom | Email : $email | Message : $message" . PHP_EOL;
        $resultatEcriture = file_put_contents('donnees.txt', $ligne, FILE_APPEND | LOCK_EX);

        $headers = "From: noreply@localhost\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();
        $body =
            "Vous avez reçu un nouveau message :\n\n" .
            "Nom : $nom\nEmail : $email\nMessage :\n$message\n\nEnvoyé le : $dateHeure\n";

        $mailEnvoye = @mail(
            'omaromaranekrif@gmail.com',
            'Nouveau message depuis le formulaire Traitement',
            $body,
            $headers
        );
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TP PHP</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f6f8;
            color: #333;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1, h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        ul {
            text-align: left;
            padding-left: 20px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">

<?php if (!$isPost): ?>

    <h1>Formulaire de contact</h1>
    <form method="post">
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="message">Message</label>
        <textarea name="message" id="message" rows="5" required></textarea>

        <button type="submit">Envoyer</button>
    </form>

<?php elseif ($errors): ?>

    <p><?= $errors[0] ?></p>
    <a href="traitement.php">Retour</a>

<?php else: ?>

    <p><?= $resultatEcriture !== false ? "Vos données ont été enregistrées dans le fichier donnees.txt." : "Erreur lors de l'enregistrement." ?></p>
    <p><?= $mailEnvoye ? "Un email a été envoyé." : "Email non confirmé." ?></p>

    <h2>Récapitulatif</h2>
    <ul>
        <li><strong>Nom :</strong> <?= $nom ?></li>
        <li><strong>Email :</strong> <?= $email ?></li>
        <li><strong>Message :</strong> <?= nl2br($message) ?></li>
    </ul>

    <a href="traitement.php">Retour au formulaire</a>

<?php endif; ?>

</div>
</body>
</html>
