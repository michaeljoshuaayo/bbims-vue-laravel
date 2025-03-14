<template>
  <div>
    <h1>Blood Usage Forecast (Next 5 Days)</h1>

    <!-- Display each of the 5 forecast points -->
    <p>Forecast for Next Day 1: {{ forecastResults[0] }}</p>
    <p>Forecast for Next Day 2: {{ forecastResults[1] }}</p>
    <p>Forecast for Next Day 3: {{ forecastResults[2] }}</p>
    <p>Forecast for Next Day 4: {{ forecastResults[3] }}</p>
    <p>Forecast for Next Day 5: {{ forecastResults[4] }}</p>

    <!-- Chart container -->
    <div class="chart-container">
      <canvas ref="bloodUsageChart"></canvas>
    </div>
  </div>
</template>

<script>
import sarimaForecast from '@/services/sarimaForecast.js'; // Adjust path if needed

// Import Chart.js
import { Chart, registerables } from 'chart.js';  
Chart.register(...registerables);

export default {
  name: 'BloodForecast',
  data() {
    return {
      bloodUsage: [],           // Full historical usage array
      forecastResults: [],      // Will store 5 forecast values
      chart: null               // Chart.js instance
    };
  },
  created() {
    // Fetch the FULL dataset from your API
    fetch('http://127.0.0.1:8000/api/sarima-predict')
      .then(response => response.json())
      .then(data => {
        this.bloodUsage = data;
        // Once the data is loaded, compute the 5-day forecast
        this.computeForecast();
      })
      .catch(error => {
        console.error('Error fetching usage data:', error);
      });
  },
  methods: {
    computeForecast() {
      // Use the FULL time series for the forecast
      const resids = new Array(this.bloodUsage.length).fill(0);

      // We request 5 forecast steps
      const forecastArray = sarimaForecast({
        data: this.bloodUsage,
        resids,
        p: 1, d: 1, q: 1,
        P: 1, D: 1, Q: 1,
        s: 7,        // weekly seasonality if appropriate
        steps: 5,    // <-- Forecast 5 days
        phi: 0,
        Phi: 0,
        theta: 0,
        Theta: 0
      });

      // Now we have 5 forecasted values
      this.forecastResults = forecastArray; // e.g. [f1, f2, f3, f4, f5]

      // Render the chart with the last 14 days of history + 5 forecast points
      this.renderChart();
    },

    renderChart() {
      // Ensure the <canvas> ref exists
      if (!this.$refs.bloodUsageChart) return;

      // Destroy existing chart if it exists (for re-renders)
      if (this.chart) {
        this.chart.destroy();
      }

      const ctx = this.$refs.bloodUsageChart.getContext('2d');

      // Number of historical days to display
      const LAST_N = 14;

      // The forecast covers 5 points
      const FORECAST_LENGTH = this.forecastResults.length; // should be 5

      // Slice the last 14 days from the full usage array
      const recentUsage = this.bloodUsage.slice(-LAST_N);

      // We'll plot "recentUsage.length" data points for history,
      // then 5 forecast points. For the forecast dataset, we insert
      // `null` for historical range so the red line only appears at the end.
      const forecastData = [
        ...Array(recentUsage.length).fill(null),
        ...this.forecastResults
      ];

      // Create total label count = last N days + 5 forecast
      const totalPoints = recentUsage.length + FORECAST_LENGTH;
      const labels = Array.from({ length: totalPoints }, (_, i) => `Day ${i + 1}`);

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: `Last ${LAST_N} Days (Historical)`,
              data: recentUsage,
              borderColor: 'blue',
              backgroundColor: 'blue',
              fill: false,
              tension: 0.2
            },
            {
              label: 'Forecast (Next 5 Days)',
              data: forecastData,
              borderColor: 'red',
              backgroundColor: 'red',
              fill: false,
              tension: 0.2
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  }
};
</script>

<style scoped>
.chart-container {
  position: relative;
  width: 100%;
  max-width: 800px;
  height: 400px;
  margin: 0 auto;
}
</style>
