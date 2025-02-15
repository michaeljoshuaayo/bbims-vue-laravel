<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, computed } from 'vue';
import api from '@/services/api'; 
import { format, differenceInHours, differenceInDays } from 'date-fns'; 

const toast = useToast();
const dt = ref();
const products = ref([]);
const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref({ inventoryStatus: 'AVAILABLE' });
const selectedProducts = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false);
const isEditMode = ref(false); 

const inventoryStatusOptions = [
    { label: 'AVAILABLE', value: 'AVAILABLE' },
    { label: 'DISCARDED', value: 'DISCARDED' }
];

const bloodTypeOptions = [
    { label: 'A+', value: 'A+' },
    { label: 'A-', value: 'A-' },
    { label: 'B+', value: 'B+' },
    { label: 'B-', value: 'B-' },
    { label: 'AB+', value: 'AB+' },
    { label: 'AB-', value: 'AB-' },
    { label: 'O+', value: 'O+' },
    { label: 'O-', value: 'O-' }
];

const bloodComponents = [
    { label: 'Whole blood', value: 'Whole blood' },
    { label: 'Packed RBC', value: 'Packed RBC' },
    { label: 'Fresh Frozen Plasma', value: 'Fresh Frozen Plasma' },
    { label: 'Platelet Concentrate', value: 'Platelet Concentrate' }
];

const fetchBloodInventory = async () => {
    try {
        const response = await api.get('http://localhost:8000/api/blood-inventory'); 
        console.log('API Response:', response.data); 
        products.value = response.data;
    } catch (error) {
        console.error('Error fetching blood inventory:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to fetch blood inventory' });
    }
};

onMounted(() => {
    fetchBloodInventory();
});

const formattedProducts = computed(() => {
    return products.value.map(product => ({
        ...product,
        expiryDate: format(new Date(product.expiryDate), 'yyyy-MM-dd'),
        created_at: format(new Date(product.created_at), 'yyyy-MM-dd'),
        isExpiringSoon: differenceInDays(new Date(product.expiryDate), new Date()) <= 10
    }));
});

const bloodTypeCounts = computed(() => {
    const counts = {
        'A+': 0,
        'A-': 0,
        'B+': 0,
        'B-': 0,
        'AB+': 0,
        'AB-': 0,
        'O+': 0,
        'O-': 0
    };
    products.value.forEach(product => {
        if (counts[product.bloodType] !== undefined) {
            counts[product.bloodType]++;
        }
    });
    return counts;
});

const newBloodTypeCounts = computed(() => {
    const counts = {
        'A+': 0,
        'A-': 0,
        'B+': 0,
        'B-': 0,
        'AB+': 0,
        'AB-': 0,
        'O+': 0,
        'O-': 0
    };
    const now = new Date();
    products.value.forEach(product => {
        if (counts[product.bloodType] !== undefined && differenceInHours(now, new Date(product.created_at)) <= 24) {
            counts[product.bloodType]++;
        }
    });
    return counts;
});

function openNew() {
    product.value = { inventoryStatus: 'AVAILABLE' }; 
    submitted.value = false;
    isEditMode.value = false; 
    productDialog.value = true;
}

function hideDialog() {
    productDialog.value = false;
    submitted.value = false;
}

async function saveProduct() {
    submitted.value = true;
    if (product?.value.bloodSerialNumber?.trim() && product?.value.bloodType && product?.value.bloodComponent && product?.value.expiryDate) {
        try {
            const formattedExpiryDate = format(new Date(product.value.expiryDate), 'yyyy-MM-dd');
            if (product.value.id) {
                // Update existing product
                await api.put(`/blood-inventory/${product.value.id}`, {
                    ...product.value,
                    expiryDate: formattedExpiryDate
                });
                products.value[findIndexById(product.value.id)] = {
                    ...product.value,
                    expiryDate: formattedExpiryDate
                };
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Component Updated', life: 3000 });
            } else {
                // Create new product
                const response = await api.post('/blood-inventory', {
                    ...product.value,
                    expiryDate: formattedExpiryDate
                });
                product.value.id = response.data.id;
                product.value.created_at = new Date().toISOString(); // Add created_at field
                products.value.push({
                    ...product.value,
                    expiryDate: formattedExpiryDate
                });
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Component Created', life: 3000 });
            }
            productDialog.value = false;
            product.value = {};
        } catch (error) {
            console.error('Error saving product:', error);
            if (error.response && error.response.data) {
                console.error('Validation errors:', error.response.data.errors);
            }
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save Blood Component' });
        }
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: 'There are missing inputs. Please Check and Submit again.', life: 3000 });
    }
}

function editProduct(prod) {
    product.value = { ...prod };
    isEditMode.value = true;
    productDialog.value = true;
}

function findIndexById(id) {
    return products.value.findIndex((prod) => prod.id === id);
}

function confirmDeleteProduct(prod) {
    product.value = prod;
    deleteProductDialog.value = true;
}

async function deleteProduct() {
    try {
        await api.delete(`/blood-inventory/${product.value.id}`);
        products.value = products.value.filter((val) => val.id !== product.value.id);
        deleteProductDialog.value = false;
        product.value = {};
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Component Deleted', life: 3000 });
    } catch (error) {
        console.error('Error deleting product:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete Blood Component', life: 3000 });
    }
}

async function deleteSelectedProducts() {
    try {
        const ids = selectedProducts.value.map(product => product.id);
        await api.post('/blood-inventory/delete-multiple', { ids });
        products.value = products.value.filter((product) => !ids.includes(product.id));
        deleteProductsDialog.value = false;
        selectedProducts.value = [];
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Components Deleted', life: 3000 });
    } catch (error) {
        console.error('Error deleting selected products:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete selected Blood Components', life: 3000 });
    }
}

function exportCSV() {
    dt.value.exportCSV();
}

function importCSV(event) {
    const file = event.target.files[0];
    if (file) {
        Papa.parse(file, {
            header: true,
            complete: async function(results) {
                try {
                    const response = await api.post('/blood-inventory/import', results.data);
                    products.value.push(...response.data);
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Blood Components Imported', life: 3000 });
                } catch (error) {
                    console.error('Error importing products:', error);
                    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to import Blood Components', life: 3000 });
                }
            },
            error: function(error) {
                console.error('Error parsing CSV:', error);
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to parse CSV file', life: 3000 });
            }
        });
    }
}

function confirmDeleteSelected() {
    deleteProductsDialog.value = true;
}
</script>

<template>
    <div>
        <div class="font-semibold text-2xl mb-6">Blood Inventory Summary</div>
        <div class="grid grid-cols-12 gap-8 mb-6">
            <div v-for="(count, bloodType) in bloodTypeCounts" :key="bloodType" class="col-span-12 lg:col-span-6 xl:col-span-3">
                <div class="card mb-0">
                    <div class="flex justify-between mb-4">
                        <div>
                            <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">{{ bloodType }}</div>
                            <span class="block text-muted-color font-medium mb-4">{{ count }} units in stock</span>
                        </div>
                        <div class="flex items-center justify-center bg-green-100 dark:bg-green-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-plus text-green-500 !text-xl"></i>
                        </div>
                    </div>
                    <span class="text-primary font-medium">{{ newBloodTypeCounts[bloodType] }} new</span>
                    <span class="text-muted-color"> since last 24 hours</span>
                </div>
            </div>
        </div>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                    <Button label="Discard" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected" :disabled="!selectedProducts || !selectedProducts.length" />
                </template>
                <template #end>
                    <input type="file" accept=".csv" @change="importCSV" style="display: none;" ref="fileInput" />
                    <Button label="Import from CSV file" icon="pi pi-download" severity="secondary" @click="$refs.fileInput.click()" class="mr-2" />
                    <Button label="Export to CSV file" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" />
                </template>
            </Toolbar>
            <DataTable
                ref="dt"
                v-model:selection="selectedProducts"
                :value="formattedProducts"
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
                        <h4 class="m-0">Manage Blood Products</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search Blood..." />
                        </IconField>
                    </div>
                </template>
                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="bloodSerialNumber" header="Blood Serial Number" style="min-width: 12rem">
                                    <template #body="slotProps">
                        <span :class="{'text-red-500': slotProps.data.isExpiringSoon}">{{ slotProps.data.bloodSerialNumber }}</span>
                    </template></Column>
                <Column field="bloodType" header="Blood Type" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                        <span :class="{'text-red-500': slotProps.data.isExpiringSoon}">{{ slotProps.data.bloodType }}</span>
                    </template></Column>
                <Column field="bloodComponent" header="Blood Component" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                        <span :class="{'text-red-500': slotProps.data.isExpiringSoon}">{{ slotProps.data.bloodComponent }}</span>
                    </template></Column>
                <Column field="created_at" header="Date Added" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                        <span :class="{'text-red-500': slotProps.data.isExpiringSoon}">{{ slotProps.data.created_at }}</span>
                    </template></Column>
                <Column field="expiryDate" header="Expiration Date" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <span :class="{'text-red-500': slotProps.data.isExpiringSoon}">{{ slotProps.data.expiryDate }}</span>
                    </template>
                </Column>
                <Column field="inventoryStatus" header="Status" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                        <span :class="{'text-red-500': slotProps.data.isExpiringSoon}">{{ slotProps.data.inventoryStatus }}</span>
                    </template></Column>
                <Column field="Days Left" header="Days Left" style="min-width: 10rem">
                    <template #body="slotProps">
                        <span :class="{'text-red-500': slotProps.data.isExpiringSoon}">
                            {{ differenceInDays(new Date(slotProps.data.expiryDate), new Date()) <= 0 ? 'EXPIRED' : differenceInDays(new Date(slotProps.data.expiryDate), new Date()) }}
                        </span>
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteProduct(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>
        <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Blood Product Details" :modal="true">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="bloodSerialNumber" class="block font-bold mb-3">Blood Serial Number</label>
                    <InputText id="bloodSerialNumber" v-model.trim="product.bloodSerialNumber" required="true" autofocus :invalid="submitted && !product.bloodSerialNumber" fluid />
                    <small v-if="submitted && !product.bloodSerialNumber" class="text-red-500">Blood Serial Number is required.</small>
                </div>
                <div>
                    <label for="bloodType" class="block font-bold mb-3">Blood Type</label>
                    <Select id="bloodType" v-model="product.bloodType" :options="bloodTypeOptions" optionLabel="label" optionValue="value" placeholder="Select Blood Type" required="true" fluid />
                    <small v-if="submitted && !product.bloodType" class="text-red-500">Blood Type is required.</small>
                </div>
                <div>
                    <label for="bloodComponent" class="block font-bold mb-3">Blood Component</label>
                    <Dropdown id="bloodComponent" v-model="product.bloodComponent" :options="bloodComponents" optionLabel="label" optionValue="value" placeholder="Select Blood Component" required="true" class="w-full" />
                    <small v-if="submitted && !product.bloodComponent" class="text-red-500">Blood Component is required.</small>
                </div>
                <div>
                    <label for="expiryDate" class="block font-bold mb-3">Expiry Date</label>
                    <DatePicker id="expiryDate" v-model="product.expiryDate" :showIcon="true" optionLabel="label" placeholder="Select Date" required="true" fluid></DatePicker>
                    <small v-if="submitted && !product.expiryDate" class="text-red-500">Expiry Date is required.</small>
                </div>
                <div>
                    <label for="inventoryStatus" class="block font-bold mb-3">Inventory Status</label>
                    <Select id="inventoryStatus" v-model="product.inventoryStatus" :options="inventoryStatusOptions" optionLabel="label" optionValue="value" placeholder="Select Status" required="true" fluid :disabled="!isEditMode"/>
                    <small v-if="submitted && !product.inventoryStatus" class="text-red-500">Inventory Status is required.</small>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveProduct" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="product">Are you sure you want to discard <b>{{ product.bloodSerialNumber }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteProduct" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="product">Are you sure you want to discard the selected products?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteProductsDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" />
            </template>
        </Dialog>
    </div>
</template>
