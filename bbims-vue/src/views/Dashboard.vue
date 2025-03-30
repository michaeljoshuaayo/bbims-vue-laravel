<template>
    <div>
        <!-- Summary Cards -->
        <div class="grid grid-cols-12 gap-8">
            <div
                class="col-span-12 lg:col-span-6 xl:col-span-3"
                v-for="card in cards"
                :key="card.title"
            >
                <div class="card mb-0">
                    <div class="flex justify-between mb-4">
                        <div>
                            <span class="block text-muted-color font-medium mb-4">{{ card.title }}</span>
                            <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">
                                {{ card.value }}
                            </div>
                        </div>
                        <div
                            :class="card.iconBgClass"
                            class="flex items-center justify-center rounded-border"
                            style="width: 2.5rem; height: 2.5rem"
                        >
                            <i :class="card.iconClass"></i>
                        </div>
                    </div>
                    <span class="text-primary font-medium">{{ card.subValue }}</span>
                    <br />
                    <span class="text-muted-color" v-html="card.subText"></span>
                </div>
            </div>
        </div>

        <!-- Distributed Blood Chart -->
        <div class="col-span-12">
            <div class="card mt-6">
                <div class="font-semibold text-xl mb-4">Distributed Blood by Type & Component</div>
                <Chart type="bar" :data="chartData" :options="chartOptions" class="h-80" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useLayout } from '@/layout/composables/layout';

const { getPrimary, getSurface, isDarkTheme } = useLayout();

const cards = ref([
    {
        title: 'Blood Units in Stock',
        value: 0,
        subValue: '',
        subText: '',
        iconClass: 'pi pi-plus text-green-500 !text-xl',
        iconBgClass: 'bg-green-100 dark:bg-green-400/10'
    },
    {
        title: 'Distributed Blood Units',
        value: 0,
        subValue: '',
        subText: '',
        iconClass: 'pi pi-truck text-orange-500 !text-xl',
        iconBgClass: 'bg-orange-100 dark:bg-orange-400/10'
    },
    {
        title: 'Blood Requests',
        value: 0,
        subValue: '',
        subText: '',
        iconClass: 'pi pi-envelope text-cyan-500 !text-xl',
        iconBgClass: 'bg-cyan-100 dark:bg-cyan-400/10'
    },
    {
        title: 'Discarded Blood Units',
        value: 0,
        subValue: '',
        subText: '',
        iconClass: 'pi pi-trash text-red-500 !text-xl',
        iconBgClass: 'bg-red-100 dark:bg-red-400/10'
    }
]);

const chartData = ref(null);
const chartOptions = ref(null);

const fetchDashboardData = async () => {
    try {
        const response = await api.get('/dashboard-data');
        const data = response.data;

        cards.value[0].value = data.bloodUnitsInStock;
        cards.value[0].subValue = `${data.newUnitsLast24Hours} new`;
        cards.value[0].subText = 'since last 24 hours';

        cards.value[1].value = data.distributedBloodUnits;
        cards.value[1].subValue = `${data.distributedBloodUnits} distributed`;
        cards.value[1].subText = 'since last week';

        cards.value[2].value = data.bloodRequests;
        cards.value[2].subValue = `${data.acceptedRequests} accepted`;
        cards.value[2].subText = `${data.pendingRequests} pending requests`;

        cards.value[3].value = data.discardedBloodUnits;
        cards.value[3].subValue = `${data.discardedBloodUnits} discarded`;
        cards.value[3].subText = 'since last 24 hours';
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
};

const fetchDistributedChartData = async () => {
    try {
        const response = await api.get('/distributed-blood-data'); // Endpoint must return grouped data
        const rawData = response.data;

        const bloodTypes = rawData.map(item => item.blood_type);
        const documentStyle = getComputedStyle(document.documentElement);

        const components = ['Whole Blood', 'Packed RBC', 'Fresh Frozen Plasma', 'Platelet Concentrate'];
        const colorVars = ['--p-primary-400', '--p-primary-300', '--p-primary-200', '--p-primary-100'];

        const datasets = components.map((component, index) => ({
            type: 'bar',
            label: component,
            backgroundColor: documentStyle.getPropertyValue(colorVars[index]),
            data: rawData.map(item => item[component] || 0),
            barThickness: 32,
            borderRadius: {
                topLeft: index === components.length - 1 ? 8 : 0,
                topRight: index === components.length - 1 ? 8 : 0
            },
            borderSkipped: true
        }));

        chartData.value = {
            labels: bloodTypes,
            datasets: datasets
        };

        const borderColor = documentStyle.getPropertyValue('--surface-border');
        const textMutedColor = documentStyle.getPropertyValue('--text-color-secondary');

        chartOptions.value = {
            maintainAspectRatio: false,
            aspectRatio: 0.8,
            scales: {
                x: {
                    stacked: true,
                    ticks: {
                        color: textMutedColor
                    },
                    grid: {
                        color: 'transparent',
                        borderColor: 'transparent'
                    }
                },
                y: {
                    stacked: true,
                    ticks: {
                        color: textMutedColor
                    },
                    grid: {
                        color: borderColor,
                        borderColor: 'transparent',
                        drawTicks: false
                    }
                }
            }
        };
    } catch (error) {
        console.error('Error fetching distributed blood chart data:', error);
    }
};

onMounted(() => {
    fetchDashboardData();
    fetchDistributedChartData();
});
</script>

<style scoped>
/* Add any custom styles here */
</style>
