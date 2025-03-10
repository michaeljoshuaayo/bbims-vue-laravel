<script setup>
import { ref, onMounted } from "vue";
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

const chartData = ref({
  labels: [],
  datasets: [],
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: { y: { beginAtZero: true } },
};

const fetchForecastData = async () => {
  try {
    const response = await axios.get("http://127.0.0.1:8000/api/forecast");
    const { past, forecast } = response.data;

    // Combine past and predicted data
    chartData.value = {
      labels: [...past.map(item => item.date), ...forecast.map(item => item.date)],
      datasets: [
        {
          label: "Actual Blood Usage (Past 7 Days)",
          data: past.map(item => item.total_used),
          borderColor: "blue",
          backgroundColor: "rgba(0, 0, 255, 0.2)",
          tension: 0.4,
        },
        {
          label: "Predicted Blood Usage",
          data: [...Array(past.length).fill(null), ...forecast.map(item => item.predicted_usage)],
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

onMounted(fetchForecastData);
</script>

<template>
  <div class="chart-container">
    <h2>Blood Usage Forecast</h2>
    <div v-if="chartData.labels.length">
      <Line :data="chartData" :options="chartOptions" />
    </div>
    <p v-else>Loading forecast data...</p>
  </div>
</template>
