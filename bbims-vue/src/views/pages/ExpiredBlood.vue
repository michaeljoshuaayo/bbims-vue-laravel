<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { onMounted, ref } from 'vue';
import { format, differenceInDays } from 'date-fns';
import api from '@/services/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { useToast } from 'primevue/usetoast';

const expiredBlood = ref([]);
const selectedExpired = ref([]);
const dt = ref();
const toast = useToast();

const deleteDialogVisible = ref(false);
const idsToDelete = ref([]);

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const fetchExpiredBlood = async () => {
  try {
    const response = await api.get('/blood-inventory'); // Fetch all blood items
    expiredBlood.value = response.data
      .filter(item => new Date(item.expiryDate) < new Date()) // Include only expired items
      .map(item => ({
        ...item,
        expiryDate: format(new Date(item.expiryDate), 'yyyy-MM-dd') // Format the expiry date
      }));
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load expired blood',
      life: 3000
    });
  }
};

const confirmDelete = (ids) => {
  idsToDelete.value = ids;
  deleteDialogVisible.value = true;
};

const deleteExpiredBlood = async () => {
  try {
    await api.post('/expired-blood/delete', { ids: idsToDelete.value });
    toast.add({ severity: 'success', summary: 'Deleted', detail: 'Expired records removed.', life: 3000 });
    deleteDialogVisible.value = false;
    idsToDelete.value = [];
    fetchExpiredBlood();
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Deletion failed.', life: 3000 });
  }
};

onMounted(() => {
  fetchExpiredBlood();
});
</script>

<template>
  <div>
    <div class="card">
      <div class="flex justify-between items-center mb-3">
        <h4></h4>
        <div class="flex gap-2">
          <Button
            label="Delete Selected"
            icon="pi pi-trash"
            severity="danger"
            :disabled="!selectedExpired.length"
            @click="confirmDelete(selectedExpired.map(item => item.id))"
          />
        </div>
      </div>

      <DataTable
        ref="dt"
        v-model:selection="selectedExpired"
        :value="expiredBlood"
        dataKey="id"
        selectionMode="multiple"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} Expired Blood"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">Expired Blood Inventory</h4>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="filters['global'].value" placeholder="Search..." autocomplete="off" />
            </IconField>
          </div>
        </template>

        <Column selectionMode="multiple" style="width: 3rem" />
        <Column field="bloodSerialNumber" header="Serial Number" sortable />
        <Column field="bloodType" header="Blood Type" sortable />
        <Column field="bloodComponent" header="Component" sortable />
        <Column field="expiryDate" header="Expiry Date" sortable />
        <Column :exportable="false" style="min-width: 8rem">
          <template #body="slotProps">
            <Button icon="pi pi-trash" rounded outlined severity="danger" @click="confirmDelete([slotProps.data.id])" />
          </template>
        </Column>

        <template #empty>
          <div class="text-center p-4 text-gray-500">No expired blood found.</div>
        </template>
      </DataTable>
    </div>

    <Dialog v-model:visible="deleteDialogVisible" header="Confirm Deletion" :modal="true" :style="{ width: '400px' }">
      <span>Are you sure you want to delete the selected expired blood record(s)?</span>
      <template #footer>
        <Button label="Cancel" icon="pi pi-times" text @click="deleteDialogVisible = false" />
        <Button label="Delete" icon="pi pi-check" severity="danger" @click="deleteExpiredBlood" />
      </template>
    </Dialog>
  </div>
</template>
