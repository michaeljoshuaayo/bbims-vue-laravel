<template>
    <div class="p-grid">
        <div class="p-col-12">
            <div class="card">
                <h1>Forecasting</h1>
                <h3>Forecasted Blood Usage for Next Week</h3>
                <ul>
                    <li v-for="(value, index) in forecastedUsage" :key="index">
                        Day {{ index + 1 }}: {{ value }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { forecastBloodDemand } from '@/services/forecasting';

const usageHistory = ref([]);
const forecastedUsage = ref([]);

const fetchUsageHistory = async () => {
    try {
        const response = await api.get('/usage-history');
        usageHistory.value = response.data;

        const bloodUsageData = usageHistory.value.map(item => item.blood_usage_amount);
        forecastedUsage.value = await forecastBloodDemand(bloodUsageData, 7);
    } catch (error) {
        console.error('Error fetching usage history:', error);
    }
};

onMounted(() => {
    fetchUsageHistory();
});
</script>