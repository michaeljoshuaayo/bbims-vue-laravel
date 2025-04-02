<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
} from "chart.js";

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

const chartData = ref({ labels: [], datasets: [] });
const componentChartData = ref({ labels: [], datasets: [] });

const selectedUsagePeriod = ref("week");
const selectedComponentPeriod = ref("week");
const selectedComponent = ref("Whole Blood");

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: { y: { beginAtZero: true } },
};

// Helper function to determine the correct label format
const getPeriodLabel = (period, pastCount, forecastCount) => {
  if (period === "week") return { pastLabel: `${pastCount} days`, forecastLabel: `${forecastCount} days` };
  if (period === "month") return { pastLabel: `${pastCount} weeks`, forecastLabel: `${forecastCount} weeks` };
  if (period === "year") return { pastLabel: `${pastCount} years`, forecastLabel: `${forecastCount} years` };
  return { pastLabel: `${pastCount} data points`, forecastLabel: `${forecastCount} data points` };
};

// Fetch Blood Usage Forecast
const fetchForecastData = async () => {
  try {
    const response = await axios.get(`https://api.bbimsbicol.com/api/forecast?period=${selectedUsagePeriod.value}`);
    const { past, forecast } = response.data;

    const pastCount = past.length;
    const forecastCount = forecast.length;

    // Correct label formatting based on the selected period
    const { pastLabel, forecastLabel } = getPeriodLabel(selectedUsagePeriod.value, pastCount, forecastCount);

    let labels = [...past.map(item => item.date || item.year), ...forecast.map(item => item.date || item.year)];
    let actualData = past.map(item => item.total_used);
    let predictedData = [...Array(past.length).fill(null), ...forecast.map(item => item.predicted_usage)];

    chartData.value = {
      labels,
      datasets: [
        {
          label: `Actual Blood Usage Past (${pastLabel})`,
          data: actualData,
          borderColor: "blue",
          backgroundColor: "rgba(0, 0, 255, 0.2)",
          tension: 0.4,
        },
        {
          label: `Predicted Blood Usage (${forecastLabel})`,
          data: predictedData,
          borderColor: "red",
          backgroundColor: "rgba(255, 0, 0, 0.2)",
          borderDash: [5, 5],
          tension: 0.4,
        },
      ],
    };
  } catch (error) {
    console.error("Error fetching forecast data:", error);
  }
};

// Fetch Blood Component Forecast
const fetchComponentForecast = async () => {
  try {
    const response = await axios.get(
      `http://127.0.0.1:8000/api/forecast/component?period=${selectedComponentPeriod.value}&component=${selectedComponent.value}`
    );
    const { past, forecast } = response.data;

    const pastCount = past.length;
    const forecastCount = forecast.length;

    const { pastLabel, forecastLabel } = getPeriodLabel(selectedComponentPeriod.value, pastCount, forecastCount);

    let labels = [...past.map(item => item.date || item.year), ...forecast.map(item => item.date || item.year)];
    let actualData = past.map(item => item.total_used);
    let predictedData = [...Array(past.length).fill(null), ...forecast.map(item => item.predicted_usage)];

    componentChartData.value = {
      labels,
      datasets: [
        {
          label: `Actual ${selectedComponent.value} Usage Past (${pastLabel})`,
          data: actualData,
          borderColor: "green",
          backgroundColor: "rgba(0, 255, 0, 0.2)",
          tension: 0.4,
        },
        {
          label: `Predicted ${selectedComponent.value} Usage (${forecastLabel})`,
          data: predictedData,
          borderColor: "purple",
          backgroundColor: "rgba(128, 0, 128, 0.2)",
          borderDash: [5, 5],
          tension: 0.4,
        },
      ],
    };
  } catch (error) {
    console.error("Error fetching component forecast data:", error);
  }
};

// Watchers for changes in period and component selection
watch(selectedUsagePeriod, fetchForecastData);
watch([selectedComponent, selectedComponentPeriod], fetchComponentForecast);

// Initial data fetch on mount
onMounted(() => {
  fetchForecastData();
  fetchComponentForecast();
});
</script>


<template>
  <div class="chart-container">
    <h2>Blood Usage Forecast</h2>

    <div class="controls">
      <label for="usage-period">Select Blood Usage Forecast Period:</label>
      <select id="usage-period" v-model="selectedUsagePeriod">
        <option value="week">Week</option>
        <option value="month">Month</option>
        <option value="year">Year</option>
      </select>
    </div>

    <div v-if="chartData.labels.length" class="chart-wrapper">
      <Line :data="chartData" :options="chartOptions" />
    </div>
    <p v-else>Loading forecast data...</p>

    <div class="mt-12 mb-12">
        <hr>
    </div>

    <h2>Blood Component Usage Forecast</h2>

    <div class="controls">
      <label for="component-period">Select Component Forecast Period:</label>
      <select id="component-period" v-model="selectedComponentPeriod">
        <option value="week">Week</option>
        <option value="month">Month</option>
        <option value="year">Year</option>
      </select>
    </div>

    <div class="controls">
      <label for="component">Select Blood Component:</label>
      <select id="component" v-model="selectedComponent">
        <option>Whole Blood</option>
        <option>Packed RBC</option>
        <option>Fresh Frozen Plasma</option>
        <option>Platelet Concentrate</option>
      </select>
    </div>

    <div v-if="componentChartData.labels.length" class="chart-wrapper">
      <Line :data="componentChartData" :options="chartOptions" />
    </div>
    <p v-else>Loading component forecast data...</p>
  </div>
</template>

<style scoped>
.chart-container {
  max-width: 100%;
  width: 100%;
  margin: auto;
  text-align: center;
}

.chart-wrapper {
  width: 100%;
  height: 400px;
}

.controls {
  margin-bottom: 15px;
  font-size: 18px;
}

select {
  font-size: 16px;
  padding: 8px;
}
</style>
