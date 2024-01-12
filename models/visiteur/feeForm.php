<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fiche de frais</title>
    <link rel="stylesheet" type="text/css" href="fiche.css">
</head>
<body>
    <button onclick="toggleModal()" id="openModalBtn">Ouvrir le formulaire</button>

    <script>
        let isOpen = false;

        function toggleModal() {
            isOpen = !isOpen;
            updateContent();
            updateButtonText();
        }

        function updateContent() {
            const modalContainer = document.getElementById('modalContainer');
            const content = isOpen ? `
                <div id="modal" class="modal">
                    <div class="modal-content">
                        <h1>Fiche de frais</h1>
                        <label for="date">Date :</label>
                        <input type="date" id="date" name="date" required><br><br>
    
                        <label for="description">Description :</label>
                        <input type="text" id="description" name="description" required><br><br>
    
                        <label for="montant">Montant :</label>
                        <input type="number" id="montant" name="montant" step="0.01" required><br><br>
    
                        <label for="categorie">Catégorie :</label>
                        <select id="categorie" name="categorie">
                            <option value="repas">Repas</option>
                            <option value="transport">Transport</option>
                            <option value="hébergement">Hébergement</option>
                            <option value="autre">Autre</option>
                        </select><br><br>
    
                        <label for="justificatif">Justificatif :</label>
                        <input type="file" id="justificatif" name="justificatif"><br><br>
    
                        <label for="commentaire">Commentaire :</label><br>
                        <textarea id="commentaire" name="commentaire" rows="4" cols="50"></textarea><br><br>
    
                        <input type="submit" value="Soumettre">
                    </div>
                </div>` : '';

            modalContainer.innerHTML = content;
        }

        function updateButtonText() {
            const buttonText = isOpen ? "Fermer le formulaire" : "Ouvrir le formulaire";
            document.getElementById('openModalBtn').innerText = buttonText;
        }
    </script>

    <div id="modalContainer"></div>

    <script src="fiche.js"></script>
</body>
</html>
