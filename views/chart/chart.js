export function createChart(chartData, chartOptions, chartElementId) {
  // Créer une instance de chart avec les données et les options fournies
  const chart = new Chart(chartElementId, {
    type: chartOptions.type,
    data: chartData,
    options: chartOptions
  });

  // Retourner l'instance du chart créé
  return chart;
}
