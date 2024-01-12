    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../../styles/index.css" rel="stylesheet">
        <title>Page d'inscription</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- <link href="https://fonts.googleapis.com/css2?family=News+Cycle:wght@400;700&display=swap" rel="stylesheet"> -->
        <style>
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            input[type=number] {
                -moz-appearance: textfield;
            }
        </style>
    </head>

    <body>
        <div class="main-inscription">
            <section class="inscription-container">
                <h3>Bienvenue sur FeeFinder !</h3>
                <form action="../../models/inscription/register.php" method="post">
                    <label>nom d'utilisateur</label><br />
                    <input type="text" name="login" class="login" placeholder="entrez votre id"><br />
                    <label>nom</label> <br />
                    <input type="text" name="name" class="name" placeholder="nom de famille" required /><br />
                    <label>prénom</label> <br />
                    <input type="text" name="prenom" class="prenom" placeholder="votre prenom" required /><br />
                    <label>Adresse email</label> <br />
                    <input type="email" name="email" class="email" placeholder="Entrer votre adresse e-mail" required /> <br />
                    <label>mot de passe</label> <br />
                    <input type="password" name="password" class="password" placeholder="entrer votre mot de passe" /> <br />
                    <label>numéro de portable</label><br />
                    <input type="number" name="phone" class="phone" placeholder="votre numéro" required><br />
                    <label>adresse</label><br />
                    <input type="text" name="adress" class="adress" placeholder="votre adresse" required><br />
                    <label>code postal</label><br />
                    <input type="number" name="postal" class="postal" placeholder="code postal" required> <br />
                    <label>ville</label><br />
                    <input type="text" name="city" class="city" placeholder="votre ville" required><br />
                    <button class="button" type="submit">s'inscrire</button>
                </form>
            </section>
        </div>
    </body>

    </html>