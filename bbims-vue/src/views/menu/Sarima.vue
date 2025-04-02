<template>
  <div>
    <h1>Blood Usage Forecast</h1>

    <!-- Forecast Type -->
    <label for="forecastType">Forecast Type:</label>
    <select v-model="forecastType" @change="loadForecast" id="forecastType">
      <option value="weekly">Weekly (Next 7 Days)</option>
      <option value="monthly">Monthly (Next 30 Days)</option>
      <option value="yearly">Yearly (Next 365 Days)</option>
    </select>

    <!-- Component Dropdown -->
    <label for="selectedComponent">Component:</label>
    <select v-model="selectedComponent" @change="loadForecast" id="selectedComponent">
      <option value="Total">Total</option>
      <option v-for="(values, name) in rawComponentUsage" :key="name" :value="name">
        {{ name }}
      </option>
    </select>

    <!-- Chart -->
    <div class="chart-container">
      <canvas ref="forecastChart"></canvas>
    </div>
  </div>
</template>

<script>
import sarimaForecast from '@/services/sarimaForecast.js';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  name: 'BloodForecast',
  data() {
    return {
      rawTotalUsage: [],
      rawComponentUsage: {},
      selectedComponent: 'Total',
      forecastType: 'weekly',
      chart: null
    };
  },
  mounted() {
    this.fetchAllUsage();
  },
  methods: {
    fetchAllUsage() {
      // Get total usage
      fetch('https://api.bbimsbicol.com/api/sarima-predict')
        .then(res => res.json())
        .then(data => {
          this.rawTotalUsage = data;
          this.loadForecast();
        });

      // Get component-wise usage
      fetch('https://api.bbimsbicol.com/api/sarima-components')
        .then(res => res.json())
        .then(data => {
          this.rawComponentUsage = data;
        });
    },

    loadForecast() {
      let steps = 7;
      let s = 2*365;

      if (this.forecastType === 'monthly') {
        steps = 30;
        s = 2*365;
      } else if (this.forecastType === 'yearly') {
        steps = 365;
        s = 2*365;
      }

      let usageArray =
        this.selectedComponent === 'Total'
          ? this.rawTotalUsage
          : this.rawComponentUsage[this.selectedComponent] || [];

      const resids = new Array(usageArray.length).fill(0);

      const forecastResults = sarimaForecast({
        data: usageArray,
        resids,
        p: 1, d: 1, q: 1,
        P: 1, D: 1, Q: 1,
        s,
        steps,
        phi: 0,
        Phi: 0,
        theta: 0,
        Theta: 0
      });

      this.renderForecastChart(usageArray, forecastResults);
    },

    renderForecastChart(history, forecast) {
      if (!this.$refs.forecastChart) return;
      if (this.chart) this.chart.destroy();

      const ctx = this.$refs.forecastChart.getContext('2d');
      const LAST_N = 14;
      const recent = history.slice(-LAST_N);

      const forecastData = [...Array(recent.length).fill(null), ...forecast];
      const labels = Array.from(
        { length: recent.length + forecast.length },
        (_, i) => `Day ${i + 1}`
      );

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'Historical',
              data: recent,
              borderColor: 'blue',
              fill: false,
              tension: 0.2
            },
            {
              label: `Forecast (${this.selectedComponent})`,
              data: forecastData,
              borderColor: 'red',
              fill: false,
              tension: 0.2
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { beginAtZero: true }
          },
          plugins: {
            legend: { position: 'bottom' }
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
  max-width: 1500px;
  height: 400px;
  margin: 2rem auto;
}

#forecastType,
#selectedComponent {
  margin: 1rem 1rem 1rem 0;
  padding: 0.4rem;
  font-size: 1rem;
}
</style>
