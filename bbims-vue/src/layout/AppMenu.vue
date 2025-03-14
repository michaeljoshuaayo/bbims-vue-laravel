<script setup>
import { ref, computed } from 'vue';
import AppMenuItem from './AppMenuItem.vue';
import { useAuth } from 'vue-auth3';

const auth = useAuth();
const user = ref(null);

const getUserData = async () => {
    try {
        const res = await auth.fetch();
        console.log('User data:', res.data);
        user.value = res.data[0];
    } catch (error) {
        console.error('Error fetching user data:', error);
    }
};

const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/' }]
    },
    {
        label: 'Menu',
        items: [
            { label: 'RIS Form', icon: 'pi pi-fw pi-id-card', to: '/menu/RIS-Form', for: 'user' },
            { label: 'Blood Requests', icon: 'pi pi-fw pi-envelope', to: '/menu/blood-request-list', for: 'admin' },
            { label: 'Blood Requests', icon: 'pi pi-fw pi-envelope', to: '/menu/blood-request-list', for: 'staff' },
            { label: 'User List', icon: 'pi pi-fw pi-user-edit', to: '/menu/user-list', for: 'admin'},
            { label: 'User List', icon: 'pi pi-fw pi-user-edit', to: '/menu/user-list', for: 'staff'},
            { label: 'Usage History', icon: 'pi pi-fw pi-history', to: '/menu/usage-history', for: 'admin' },
            { label: 'Usage History', icon: 'pi pi-fw pi-history', to: '/menu/usage-history', for: 'staff' },
            { label: 'Forecasting', icon: 'pi pi-fw pi-chart-line', to: '/menu/forecasting', for: 'admin' },
            { label: 'Forecasting', icon: 'pi pi-fw pi-chart-line', to: '/menu/forecasting', for: 'staff' },
            { label: 'Request History', icon: 'pi pi-fw pi-history', to: '/menu/request-history', for: 'user' },
            { label: 'Sarima', icon: 'pi pi-fw pi-chart-line', to: '/menu/sarima', for: 'admin' },
            { label: 'Sarima', icon: 'pi pi-fw pi-chart-line', to: '/menu/sarima', for: 'staff' },


        ]
    },
    {
        label: 'Inventory',
        icon: 'pi pi-fw pi-briefcase',
        to: '/pages',
        items: [
            {
                label: 'Blood Inventory',
                icon: 'pi pi-fw pi-pencil',
                to: '/pages/blood-inventory',
                for: 'staff'
            },
            {
                label: 'Blood Inventory',
                icon: 'pi pi-fw pi-pencil',
                to: '/pages/blood-inventory',
                for: 'admin'
            },
            {
                label: 'Expired Blood',
                icon: 'pi pi-fw pi-trash',
                to: '/pages/expired-blood',
                for: 'admin'
            },
            {
                label: 'Expired Blood',
                icon: 'pi pi-fw pi-trash',
                to: '/pages/expired-blood',
                for: 'staff'
            }
        ]
    }
]);

const filteredModel = computed(() => {
    if (!user.value) return model.value;

    return model.value
        .map(section => {
            const filteredItems = section.items.filter(item => {
                return !item.for || item.for === user.value.role;
            });

            return { ...section, items: filteredItems };
        })
        .filter(section => {
            if (section.label === 'Inventory' && !['admin', 'staff'].includes(user.value.role)) {
                return false;
            }
            return true;
        });
});

getUserData();
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in filteredModel" :key="item.label">
            <app-menu-item :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
