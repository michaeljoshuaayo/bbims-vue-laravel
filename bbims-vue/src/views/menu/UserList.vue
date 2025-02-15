<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, computed } from 'vue';
import { format, parseISO } from 'date-fns';
import api from '@/services/api'; 

const toast = useToast();
const dt = ref();
const users = ref([]);
const userDialog = ref(false);
const deleteUserDialog = ref(false);
const deleteUsersDialog = ref(false);
const user = ref({});
const selectedUsers = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false);
const isEditMode = ref(false); 

const roles = ref([
    { label: 'Admin', value: 'admin' },
    { label: 'Staff', value: 'staff' },
    { label: 'User', value: 'user' }
]);

const fetchUsers = async () => {
    try {
        const response = await api.get('/users'); 
        console.log('API Response:', response.data); 
        users.value = response.data;
    } catch (error) {
        console.error('Error fetching users:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to fetch users' });
    }
};

onMounted(() => {
    fetchUsers();
});

const formattedUsers = computed(() => {
    return users.value.map(user => ({
        ...user,
        created_at: user.created_at ? format(parseISO(user.created_at), 'yyyy-MM-dd') : 'invalid date'
    }));
});

function openNew() {
    user.value = {}; 
    submitted.value = false;
    isEditMode.value = false; 
    userDialog.value = true;
}

function hideDialog() {
    userDialog.value = false;
    submitted.value = false;
}

async function saveUser() {
    submitted.value = true;
    if (user?.value.name?.trim() && user?.value.email?.trim()) {
        try {
            if (user.value.id) {
                // Update existing user
                await api.put(`/users/${user.value.id}`, user.value);
                users.value[findIndexById(user.value.id)] = user.value;
                toast.add({ severity: 'success', summary: 'Successful', detail: 'User Updated', life: 3000 });
            } else {
                // Create new user
                const response = await api.post('/users', user.value);
                user.value.id = response.data.id;
                users.value.push(user.value);
                toast.add({ severity: 'success', summary: 'Successful', detail: 'User Created', life: 3000 });
            }
            userDialog.value = false;
            user.value = {};
        } catch (error) {
            console.error('Error saving user:', error);
            if (error.response && error.response.data) {
                console.error('Validation errors:', error.response.data.errors);
            }
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save user' });
        }
    }
}

function editUser(usr) {
    user.value = { ...usr };
    isEditMode.value = true;
    userDialog.value = true;
}

function findIndexById(id) {
    return users.value.findIndex((usr) => usr.id === id);
}

function confirmDeleteUser(usr) {
    user.value = usr;
    deleteUserDialog.value = true;
}

async function deleteUser() {
    try {
        await api.delete(`/users/${user.value.id}`);
        users.value = users.value.filter((val) => val.id !== user.value.id);
        deleteUserDialog.value = false;
        user.value = {};
        toast.add({ severity: 'success', summary: 'Successful', detail: 'User Deleted', life: 3000 });
    } catch (error) {
        console.error('Error deleting user:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete user', life: 3000 });
    }
}

function confirmDeleteSelected() {
    deleteUsersDialog.value = true;
}

async function deleteSelectedUsers() {
    try {
        const ids = selectedUsers.value.map(user => user.id);
        await api.post('/users/delete-multiple', { ids });
        users.value = users.value.filter((val) => !selectedUsers.value.includes(val));
        deleteUsersDialog.value = false;
        selectedUsers.value = null;
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Users Deleted', life: 3000 });
    } catch (error) {
        console.error('Error deleting selected users:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete selected users' });
    }
}
</script>

<template>
    <div>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                    <Button label="Remove" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected" :disabled="!selectedUsers || !selectedUsers.length" />
                </template>
            </Toolbar>
            <DataTable
                ref="dt"
                v-model:selection="selectedUsers"
                :value="formattedUsers"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} Users"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Manage User List</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search Users..." autocomplete="off" />
                        </IconField>
                    </div>
                </template>
                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="name" header="Name" sortable style="min-width: 12rem"></Column>
                <Column field="role" header="User Role" sortable style="min-width: 12rem"></Column>
                <Column field="facility" header="Facility" sortable style="min-width: 12rem"></Column>
                <Column field="email" header="Email" sortable style="min-width: 12rem"></Column>
                <Column field="created_at" header="Date Created" sortable style="min-width: 10rem"></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editUser(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteUser(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>
        <Dialog v-model:visible="userDialog" :style="{ width: '450px' }" header="User Details" :modal="true">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="name" class="block font-bold mb-3">Name</label>
                    <InputText id="name" v-model.trim="user.name" required="true" autofocus :invalid="submitted && !user.name" fluid autocomplete="off" />
                    <small v-if="submitted && !user.name" class="text-red-500">Name is required.</small>
                </div>
                <div>
                    <label for="role" class="block font-bold mb-3">User Role</label>
                    <Dropdown id="role" v-model="user.role" :options="roles" optionLabel="label" optionValue="value" placeholder="Select a Role" required="true" fluid autocomplete="off" />
                    <small v-if="submitted && !user.role" class="text-red-500">User Role is required.</small>
                </div>
                <div>
                    <label for="facility" class="block font-bold mb-3">Facility</label>
                    <InputText id="facility" v-model="user.facility" required="true" fluid autocomplete="off" />
                    <small v-if="submitted && !user.facility" class="text-red-500">Facility is required.</small>
                </div>
                <div>
                    <label for="email" class="block font-bold mb-3">Email</label>
                    <InputText id="email" v-model="user.email" required="true" fluid autocomplete="off"/>
                    <small v-if="submitted && !user.email" class="text-red-500">Email is required.</small>
                </div>
                <div>
                    <label for="password" class="block font-bold mb-3">Password</label>
                    <InputText id="password" v-model="user.password" :disabled="isEditMode" type="password" required="true" fluid autocomplete="new-password" />
                    <small v-if="submitted && !user.password" class="text-red-500">Password is required.</small>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveUser" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteUserDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="user">Are you sure you want to remove <b>{{ user.name }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteUserDialog = false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteUser" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteUsersDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="user">Are you sure you want to <b>remove</b> the selected users?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteUsersDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedUsers" />
            </template>
        </Dialog>
    </div>
</template>
