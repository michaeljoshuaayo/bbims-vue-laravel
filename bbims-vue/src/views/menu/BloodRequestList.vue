<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, computed } from 'vue';
import { format, parseISO } from 'date-fns';
import api from '@/services/api'; 

const toast = useToast();
const dt = ref();
const bloodRequests = ref([]);
const bloodRequestDialog = ref(false);
const deleteBloodRequestDialog = ref(false);
const deleteBloodRequestsDialog = ref(false);
const viewRequisitionItemsDialog = ref(false);
const bloodRequest = ref({});
const requisitionItems = ref([]);
const selectedBloodRequests = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false);
const isEditMode = ref(false); 

const fetchBloodRequests = async () => {
    try {
        const response = await api.get('/blood-requests'); 
        console.log('API Response:', response.data); 
        bloodRequests.value = response.data;
    } catch (error) {
        console.error('Error fetching blood requests:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to fetch blood requests' });
    }
};

const fetchRequisitionItems = async (bloodRequestId) => {
    try {
        const response = await api.get(`/blood-requests/${bloodRequestId}/requisition-items`);
        requisitionItems.value = response.data;
        viewRequisitionItemsDialog.value = true;
    } catch (error) {
        console.error('Error fetching requisition items:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to fetch requisition items' });
    }
};

onMounted(() => {
    fetchBloodRequests();
});

const formattedBloodRequests = computed(() => {
    return bloodRequests.value.map(request => ({
        ...request,
        created_at: request.created_at ? format(parseISO(request.created_at), 'yyyy-MM-dd') : 'invalid date'
    }));
});

function openNew() {
    bloodRequest.value = {}; 
    submitted.value = false;
    isEditMode.value = false; 
    bloodRequestDialog.value = true;
}

function hideDialog() {
    bloodRequestDialog.value = false;
    submitted.value = false;
}

async function saveBloodRequest() {
    submitted.value = true;
    if (bloodRequest?.value.requesting_facility?.trim() && bloodRequest?.value.address?.trim()) {
        try {
            if (bloodRequest.value.id) {
                // Update existing blood request
                await api.put(`/blood-requests/${bloodRequest.value.id}`, bloodRequest.value);
                bloodRequests.value[findIndexById(bloodRequest.value.id)] = bloodRequest.value;
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Request Updated', life: 3000 });
            } else {
                // Create new blood request
                const response = await api.post('/blood-requests', bloodRequest.value);
                bloodRequest.value.id = response.data.id;
                bloodRequests.value.push(bloodRequest.value);
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Request Created', life: 3000 });
            }
            bloodRequestDialog.value = false;
            bloodRequest.value = {};
        } catch (error) {
            console.error('Error saving blood request:', error);
            if (error.response && error.response.data) {
                console.error('Validation errors:', error.response.data.errors);
            }
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save blood request', life: 3000 });
        }
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in all required fields', life: 3000 });
    }
}

function editBloodRequest(request) {
    bloodRequest.value = { ...request };
    isEditMode.value = true;
    bloodRequestDialog.value = true;
}

function findIndexById(id) {
    return bloodRequests.value.findIndex((request) => request.id === id);
}

function confirmDeleteBloodRequest(request) {
    bloodRequest.value = request;
    deleteBloodRequestDialog.value = true;
}

async function deleteBloodRequest() {
    try {
        await api.delete(`/blood-requests/${bloodRequest.value.id}`);
        bloodRequests.value = bloodRequests.value.filter((val) => val.id !== bloodRequest.value.id);
        deleteBloodRequestDialog.value = false;
        bloodRequest.value = {};
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Request Deleted', life: 3000 });
    } catch (error) {
        console.error('Error deleting blood request:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete blood request', life: 3000 });
    }
}

function confirmDeleteSelected() {
    deleteBloodRequestsDialog.value = true;
}

async function deleteSelectedBloodRequests() {
    try {
        const ids = selectedBloodRequests.value.map(request => request.id);
        await api.post('/blood-requests/delete-multiple', { ids });
        bloodRequests.value = bloodRequests.value.filter((val) => !selectedBloodRequests.value.includes(val));
        deleteBloodRequestsDialog.value = false;
        selectedBloodRequests.value = null;
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Requests Deleted', life: 3000 });
    } catch (error) {
        console.error('Error deleting selected blood requests:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete selected blood requests' });
    }
}
</script>

<template>
    <div>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                    <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected" :disabled="!selectedBloodRequests || !selectedBloodRequests.length" />
                </template>
            </Toolbar>
            <DataTable
                ref="dt"
                v-model:selection="selectedBloodRequests"
                :value="formattedBloodRequests"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} Blood Requests"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Manage Blood Requests</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search Blood Requests..." autocomplete="off" />
                        </IconField>
                    </div>
                </template>
                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="requesting_facility" header="Requesting Facility" sortable style="min-width: 12rem"></Column>
                <Column field="address" header="Address" sortable style="min-width: 12rem"></Column>
                <Column field="pathologist" header="Pathologist" sortable style="min-width: 12rem"></Column>
                <Column field="facility_transac_num" header="Transaction Number" sortable style="min-width: 12rem"></Column>
                <Column field="requested_by" header="Requested By" sortable style="min-width: 12rem"></Column>
                <Column field="created_at" header="Date Created" sortable style="min-width: 10rem"></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="fetchRequisitionItems(slotProps.data.id)" />
                        <!-- <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editBloodRequest(slotProps.data)" /> -->
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteBloodRequest(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>
        <Dialog v-model:visible="bloodRequestDialog" :style="{ width: '450px' }" header="Blood Request Details" :modal="true">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="requesting_facility" class="block font-bold mb-3">Requesting Facility</label>
                    <InputText id="requesting_facility" v-model.trim="bloodRequest.requesting_facility" required="true" autofocus :invalid="submitted && !bloodRequest.requesting_facility" fluid autocomplete="off" />
                    <small v-if="submitted && !bloodRequest.requesting_facility" class="text-red-500">Requesting Facility is required.</small>
                </div>
                <div>
                    <label for="address" class="block font-bold mb-3">Address</label>
                    <InputText id="address" v-model="bloodRequest.address" required="true" fluid autocomplete="off" />
                    <small v-if="submitted && !bloodRequest.address" class="text-red-500">Address is required.</small>
                </div>
                <div>
                    <label for="pathologist" class="block font-bold mb-3">Pathologist</label>
                    <InputText id="pathologist" v-model="bloodRequest.pathologist" required="true" fluid autocomplete="off" />
                    <small v-if="submitted && !bloodRequest.pathologist" class="text-red-500">Pathologist is required.</small>
                </div>
                <div>
                    <label for="facility_transac_num" class="block font-bold mb-3">Transaction Number</label>
                    <InputText id="facility_transac_num" v-model="bloodRequest.facility_transac_num" required="true" fluid autocomplete="off" />
                    <small v-if="submitted && !bloodRequest.facility_transac_num" class="text-red-500">Transaction Number is required.</small>
                </div>
                <div>
                    <label for="requested_by" class="block font-bold mb-3">Requested By</label>
                    <InputText id="requested_by" v-model="bloodRequest.requested_by" required="true" fluid autocomplete="off" />
                    <small v-if="submitted && !bloodRequest.requested_by" class="text-red-500">Requested By is required.</small>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveBloodRequest" />
            </template>
        </Dialog>

        <Dialog v-model:visible="viewRequisitionItemsDialog" :style="{ width: '450px' }" header="Requisition Items" :modal="true">
            <div class="flex flex-col gap-6">
                <DataTable :value="requisitionItems" dataKey="id" :paginator="true" :rows="10">
                    <Column field="blood_component" header="Blood Component" sortable style="min-width: 12rem"></Column>
                    <Column field="blood_type" header="Blood Type" sortable style="min-width: 12rem"></Column>
                    <Column field="quantity" header="Quantity" sortable style="min-width: 12rem"></Column>
                    <Column field="remarks" header="Remarks" sortable style="min-width: 12rem"></Column>
                </DataTable>
            </div>
            <template #footer>
                <Button label="Close" icon="pi pi-times" text @click="viewRequisitionItemsDialog = false" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteBloodRequestDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="bloodRequest">Are you sure you want to delete <b>{{ bloodRequest.requesting_facility }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteBloodRequestDialog = false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteBloodRequest" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteBloodRequestsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="bloodRequest">Are you sure you want to delete the selected blood requests?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteBloodRequestsDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedBloodRequests" />
            </template>
        </Dialog>
    </div>
</template>