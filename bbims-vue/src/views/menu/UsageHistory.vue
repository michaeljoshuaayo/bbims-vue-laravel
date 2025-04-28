<template>
    <div class="p-grid">
        <div class="p-col-12">
            <div class="card">
                <div class="p-grid p-ai-center p-mb-3">
                    <div class="p-col-12 text-right">
                        <div style="display: inline-block; max-width: max-content;">
                            <DateRangePicker
                                id="dateRangePicker"
                                v-model="dateRange"
                                placeholder="Filter by Date Range"
                                :start-placeholder="'Start Date'"
                                :end-placeholder="'End Date'"
                                :range="true"
                                :enable-time="false"
                                :clearable="true"
                                format="MM/dd/yyyy"
                                class="p-inputtext-sm"
                            />
                        </div>
                    </div>
                </div>
                <DataTable 
                    :value="filteredUsageHistory" 
                    :paginator="true" 
                    :rows="20" 
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} Blood Distributed"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 20]"
                >
                    <Column field="blood_request_id" header="Request ID"></Column>
                    <Column field="blood_serial_number" header="Blood Serial Number"></Column>
                    <Column field="blood_type" header="Blood Type"></Column>
                    <Column field="blood_component" header="Component"></Column>
                    <Column field="remarks" header="Remarks"></Column>
                    <Column field="created_at" header="Date Used"></Column>
                    <Column field="requesting_facility" header="Requesting Facility"></Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import DateRangePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const usageHistory = ref([]);
const dateRange = ref(null); // Start with no filtering (null instead of [null, null])

const fetchUsageHistory = async () => {
    try {
        const response = await api.get('/usage-history');
        usageHistory.value = response.data;
    } catch (error) {
        console.error('Error fetching usage history:', error);
    }
};

const formattedUsageHistory = computed(() => {
    return usageHistory.value.map(item => ({
        ...item,
        created_at: new Date(item.created_at).toLocaleDateString() // Format as date only
    }));
});

const filteredUsageHistory = computed(() => {
    if (!dateRange || !dateRange[0] || !dateRange[1]) {
        return formattedUsageHistory.value; // Return all data if no date range is selected
    }
    const [startDate, endDate] = dateRange.map(date => new Date(date.toDateString())); // Strip time from date
    return formattedUsageHistory.value.filter(item => {
        const createdAt = new Date(item.created_at);
        return createdAt >= startDate && createdAt <= endDate;
    });
});

onMounted(() => {
    fetchUsageHistory();
});
</script>