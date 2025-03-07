<template>
    <div class="p-grid">
        <div class="p-col-12">
            <div class="card">
                <DataTable 
                :value="usageHistory" 
                :paginator="true" :rows="20" 
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} Blood Distributed"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[10, 20]"

                >
                    <Column field="blood_request_id" header="Request ID"></Column>
                    <Column field="blood_serial_number" header="Blood Serial Number"></Column>
                    <Column field="blood_type" header="Blood Type"></Column>
                    <Column field="blood_component" header="Component"></Column>
                    <Column field="remarks" header="Remarks"></Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const usageHistory = ref([]);

const fetchUsageHistory = async () => {
    try {
        const response = await api.get('/usage-history');
        usageHistory.value = response.data;
    } catch (error) {
        console.error('Error fetching usage history:', error);
    }
};

onMounted(() => {
    fetchUsageHistory();
});
</script>