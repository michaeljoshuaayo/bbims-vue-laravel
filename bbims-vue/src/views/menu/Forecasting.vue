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
const selectedPeriod = ref("week"); // Default period

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: { y: { beginAtZero: true } },
};

const fetchForecastData = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/forecast?period=${selectedPeriod.value}`);
    const { past, forecast } = response.data;

    let labels = [];
    let actualData = [];
    let predictedData = [];

    if (selectedPeriod.value === "year") {
      labels = [...past.map(item => item.year), ...forecast.map(item => item.year)];
      actualData = past.map(item => item.total_used);
      predictedData = [...Array(past.length).fill(null), ...forecast.map(item => item.predicted_usage)];
    } else {
      labels = [...past.map(item => item.date), ...forecast.map(item => item.date)];
      actualData = past.map(item => item.total_used);
      predictedData = [...Array(past.length).fill(null), ...forecast.map(item => item.predicted_usage)];
    }

    const periodLabel = selectedPeriod.value === "year" ? "2 Years" : selectedPeriod.value === "month" ? "60 Days" : "14 Days";
    const forecastLabel = selectedPeriod.value === "year" ? "Next 3 Years" : `Next ${selectedPeriod.value === "month" ? "30" : "7"} Days`;

    chartData.value = {
      labels,
      datasets: [
        {
          label: `Actual Blood Usage (Past ${periodLabel})`,
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

// Watch for period changes
watch(selectedPeriod, fetchForecastData);

onMounted(fetchForecastData);
</script>

<template>
  <div class="chart-container">
    <h2>Blood Usage Forecast</h2>

    <div class="controls">
      <label for="period">Select Forecast Period:</label>
      <select id="period" v-model="selectedPeriod">
        <option value="week">Week</option>
        <option value="month">Month</option>
        <option value="year">Year</option>
      </select>
    </div>

    <div v-if="chartData.labels.length" class="chart-wrapper">
      <Line :data="chartData" :options="chartOptions" />
    </div>
    <p v-else>Loading forecast data...</p>
  </div>
</template>

<style scoped>
.chart-container {
  max-width: 100%; /* Extend width to full container */
  width: 100%; /* Extend width to full container */
  margin: auto;
  text-align: center;
}

.chart-wrapper {
  width: 100%;
  height: 400px; /* Increased height */
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
