<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref } from 'vue';

onMounted(() => {
});

const toast = useToast();
const dt = ref();
const products = ref([]); // Example state, adjust as needed
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const product = ref({});
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
onMounted(() => {
    products.value = [
        {
            id: 1,
            requestingFacility: 'Sorsogon Provincial Hospital',
            requester: 'John Doe',
            totalBloodComponent: '21',
            dateRequested: '2023-12-31',
            dateAccepted: '',
            requestStatus: 'Pending'
        },
        {
            id: 2,
            requestingFacility: 'Daraga Doctors Hospital',
            requester: 'Jane Doe',
            totalBloodComponent: '26',
            dateRequested: '2023-12-30',
            dateAccepted: '2023-12-31',
            requestStatus: 'Accepted'
        }
    ];
});

function acceptProduct(prod) {
    product.value = { ...prod };
    productDialog.value = true;
    toast.add({ severity: 'success', summary: 'Success', detail: 'Blood request accepted', life: 3000 });
}


function exportCSV() {
    dt.value.exportCSV();
}

</script>

<template>
    <div>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <div class="p-input-icon-left">
                            <InputText v-model="filters['global'].value" placeholder="Search ..." />
                            <i class="pi pi-search px-3" />
                        </div>
                    </div>
                </template>

                <template #end>
                    <Button label="Export to CSV file" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                :value="products"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Requesting Facilities</h4>
                    </div>
                </template>

                <Column field="requestingFacility" header="Facility"  style="min-width: 12rem"></Column>
                <Column field="requester" header="Requester"  style="min-width: 10rem"></Column>
                <Column field="totalBloodComponent" header="Total Blood Components" style="min-width: 10rem"></Column>
                <Column field="dateRequested" header="Date Requested" sortable style="min-width: 10rem"></Column>
                <Column field="dateAccepted" header="Date Accepted" sortable style="min-width: 10rem"></Column>
                <Column field="requestStatus" header="Request Status" sortable style="min-width: 10rem"></Column>
                <Column header="Action":exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-check" outlined class="mr-2" @click="acceptProduct(slotProps.data)" style="width: 100px;"><i class="pi pi-check p-button-icon-right ml-3"></i>Accept</Button>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>

<style scoped>
/* Your styles */
</style>
