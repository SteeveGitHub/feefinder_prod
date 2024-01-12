
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $message = $_POST["message"];


    $destinataire = "rboudjelal1@myges.fr.com";

  
    $sujet = "Nouveau message de FeeFinder";

  
    $corps_message = "Nom: $nom\n";
    $corps_message .= "Email: $email\n\n";
    $corps_message .= "Message:\n$message";

    $headers = "From: $email\r\nReply-To: $email\r\n";

    
    mail($destinataire, $sujet, $corps_message, $headers);

    // header("Location: ../postLogin/postLoginView.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FeeFinder ©</title>
    <link href="../styles/index.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/384fab6fc8.js" crossorigin="anonymous"></script>
     <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 600px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <main>
        <section class="contacts-container" id="contacts">
            <h1>CONTACT</h1>
            <h2>FeeFinder ©</h2>
            <form action="contactsView.php" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Message :</label>
        <textarea id="message" name="message" rows="4" required></textarea>

        <input type="submit" value="Envoyer">
    </form>
        </section>
    </main>
        <footer>
            <p>FeeFinder © 2021</p>
        </footer>
</body>
</html>

