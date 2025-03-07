<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, computed } from 'vue';
import { format, parseISO } from 'date-fns';
import api from '@/services/api'; 
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const toast = useToast();
const dt = ref();
const bloodRequests = ref([]);
const bloodRequestDialog = ref(false);
const viewRequisitionItemsDialog = ref(false);
const insufficientInventoryDialog = ref(false);
const bloodRequest = ref({});
const requisitionItems = ref([]);
const selectedBloodRequests = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false);
const isEditMode = ref(false);
const insufficientItemsMessage = ref('');
const currentBloodRequestId = ref(null);

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

const acceptBloodRequest = async (bloodRequestId) => {
    try {
        const response = await api.put(`/blood-requests/${bloodRequestId}/accept`);
        const index = findIndexById(bloodRequestId);
        if (index !== -1) {
            bloodRequests.value[index].status = 'Accepted';
        }
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Request Accepted', life: 3000 });

        // Update blood inventory and log usage history
        await api.post(`/blood-inventory/update-and-log`, { bloodRequestId });
    } catch (error) {
        if (error.response && error.response.data.insufficientItems) {
            const insufficientItems = error.response.data.insufficientItems;
            let message = 'There is not enough blood inventory available for the following items: ';
            insufficientItems.forEach(item => {
                message += `Blood Type: ${item.blood_type}, Blood Component: ${item.blood_component}. <strong>There are <i>${item.requested_quantity} requested blood</i>, there are only <i>${item.available_quantity} blood available left.</i></strong> `;
            });

            if (insufficientItems.some(item => item.available_quantity === 0)) {
                message += '<br><br><strong>Cannot proceed with 0 available products.</strong>';
                insufficientItemsMessage.value = message;
                insufficientInventoryDialog.value = true;
            } else {
                message += '<br><br><strong>Do you want to proceed with the available quantities?</strong>';
                insufficientItemsMessage.value = message;
                currentBloodRequestId.value = bloodRequestId;
                insufficientInventoryDialog.value = true;
            }
        } else {
            console.error('Error accepting blood request:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to accept blood request', life: 3000 });
        }
    }
};

const proceedWithAvailableQuantities = async () => {
    try {
        await api.put(`/blood-requests/${currentBloodRequestId.value}/accept`, { force: true });
        const index = findIndexById(currentBloodRequestId.value);
        if (index !== -1) {
            bloodRequests.value[index].status = 'Accepted';
        }
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Request Accepted with available quantities', life: 3000 });

        // Update blood inventory and log usage history
        await api.post(`/blood-inventory/update-and-log`, { bloodRequestId: currentBloodRequestId.value });
    } catch (error) {
        console.error('Error proceeding with available quantities:', error);
        // toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to proceed with available quantities', life: 3000 });
    } finally {
        insufficientInventoryDialog.value = false;
    }
};

onMounted(() => {
    fetchBloodRequests();
});

const formattedBloodRequests = computed(() => {
    return bloodRequests.value
        .slice()
        .sort((a, b) => (a.status === 'Pending' ? -1 : 1))
        .map(request => ({
            ...request,
            created_at: request.created_at ? format(parseISO(request.created_at), 'yyyy-MM-dd') : 'invalid date'
        }));
});

function hideDialog() {
    bloodRequestDialog.value = false;
    submitted.value = false;
}

function findIndexById(id) {
    return bloodRequests.value.findIndex((request) => request.id === id);
}
</script>

<template>
    <div>
        <div class="card">
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
                <Column field="id" header="Request ID" sortable style="min-width: 8rem"></Column>
                <Column field="requesting_facility" header="Requesting Facility" sortable style="min-width: 12rem"></Column>
                <Column field="address" header="Address" sortable style="min-width: 12rem"></Column>
                <Column field="pathologist" header="Pathologist" sortable style="min-width: 12rem"></Column>
                <Column field="facility_transac_num" header="Transaction Number" sortable style="min-width: 12rem"></Column>
                <Column field="requested_by" header="Requested By" sortable style="min-width: 12rem"></Column>
                <Column field="created_at" header="Date Created" sortable style="min-width: 10rem"></Column>
                <Column field="status" header="Status" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <span :class="{'text-green-500': slotProps.data.status === 'Accepted','text-orange-500': slotProps.data.status === 'Pending'}">{{ slotProps.data.status }}</span>
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="fetchRequisitionItems(slotProps.data.id)" />
                        <Button v-if="slotProps.data.status !== 'Accepted'" icon="pi pi-check" outlined rounded class="mr-2" @click="acceptBloodRequest(slotProps.data.id)" />
                        <!-- <Button iAcon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteBloodRequest(slotProps.data)" /> -->
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

        <Dialog v-model:visible="insufficientInventoryDialog" :style="{ width: '450px' }" header="Insufficient Inventory" :modal="true">
            <div class="flex flex-col gap-6">
                <p v-html="insufficientItemsMessage"></p>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="insufficientInventoryDialog = false" />
                <Button label="Proceed" icon="pi pi-check" @click="proceedWithAvailableQuantities" />
            </template>
        </Dialog>
    </div>
</template>