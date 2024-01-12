<div>
    <canvas id="myChart"></canvas>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
    // Vérifier si le graphique a déjà été initialisé
if (!document.getElementById('transportRepasChart').hasAttribute('data-chart-initialized')) {
    // Si ce n'est pas déjà initialisé, procéder à l'initialisation du graphique
    const transportRepasCtx = document.getElementById('transportRepasChart').getContext('2d');

    new Chart(transportRepasCtx, {
        type: 'pie',
        data: {
            labels: ['Hébergement', 'Repas'],
            datasets: [{
                data: [<?= $transportRepasData['totalHebergement'] ?>, <?= $transportRepasData['totalRepas'] ?>],
                backgroundColor: ['blue', 'orange'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Marquer le graphique comme étant initialisé
    document.getElementById('transportRepasChart').setAttribute('data-chart-initialized', 'true');
}


    document.getElementById('transportRepasChart').setAttribute('data-chart-initialized', 'true');
}
  </script>
  