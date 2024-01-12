<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Employés</title>
</head>

<body>
    <h1>Liste des Employés</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Numéro</th>
        </tr>

        <?php
        foreach ($employes as $employe) {
            echo "<tr>";
            echo "<td>" . $employe['id'] . "</td>";
            echo "<td>" . $employe['nom'] . "</td>";
            echo "<td>" . $employe['prenom'] . "</td>";
            echo "<td>" . $employe['numero'] . "</td>";
            echo "</tr>";
        }
        ?>

    </table>
</body>

</html>