<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12 lg:col-span-6 xl:col-span-3" v-for="card in cards" :key="card.title">
            <div class="card mb-0">
                <div class="flex justify-between mb-4">
                    <div>
                        <span class="block text-muted-color font-medium mb-4">{{ card.title }}</span>
                        <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">{{ card.value }}</div>
                    </div>
                    <div :class="card.iconBgClass" class="flex items-center justify-center rounded-border" style="width: 2.5rem; height: 2.5rem">
                        <i :class="card.iconClass"></i>
                    </div>
                </div>
                <span class="text-primary font-medium">{{ card.subValue }} </span>
                <br>
                <span class="text-muted-color" v-html="card.subText"></span>
            </div>
        </div>
    </div>
</template>

<script>
import api from '@/services/api'; // Import the configured Axios instance

export default {
    data() {
        return {
            cards: [
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
            ]
        };
    },
    mounted() {
        this.fetchDashboardData();
    },
    methods: {
        async fetchDashboardData() {
            try {
                const response = await api.get('/dashboard-data');
                const data = response.data;

                this.cards[0].value = data.bloodUnitsInStock;
                this.cards[0].subValue = `${data.newUnitsLast24Hours} new`;
                this.cards[0].subText = 'since last 24 hours';

                this.cards[1].value = data.distributedBloodUnits;
                this.cards[1].subValue = `${data.distributedBloodUnits} distributed`;
                this.cards[1].subText = 'since last week';

                this.cards[2].value = data.bloodRequests;
                this.cards[2].subValue = `${data.acceptedRequests} accepted`;
                this.cards[2].subText = `${data.pendingRequests} pending requests`;

                this.cards[3].value = data.discardedBloodUnits;
                this.cards[3].subValue = `${data.discardedBloodUnits} discarded`;
                this.cards[3].subText = ' since last 24 hours';
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
            }
        }
    }
};
</script>

<style scoped>
/* Add any additional styles here */
</style>
