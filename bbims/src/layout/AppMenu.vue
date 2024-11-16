<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/' }]
    },
    {
        label: 'Menu',
        items: [
            { label: 'RIS Form', icon: 'pi pi-fw pi-id-card', to: '/menu/RISForm' },
            { label: 'Blood Request Status', icon: 'pi pi-fw pi-calendar', to: '/menu/timeline' },
            { label: 'Blood Requests', icon: 'pi pi-fw pi-table', to: '/menu/bloodrequests' },
            { label: 'Analytics', icon: 'pi pi-fw pi-chart-bar', to: '/menu/charts' }
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
                to: '/pages/crud'
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
