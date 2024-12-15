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
            { label: 'RIS Form', icon: 'pi pi-fw pi-id-card', to: '/menu/RIS-Form' },
            { label: 'Blood Request Status', icon: 'pi pi-fw pi-calendar', to: '/menu/timeline' },
            { label: 'Blood Requests', icon: 'pi pi-fw pi-envelope', to: '/menu/blood-requests' },
            { label: 'Analytics', icon: 'pi pi-fw pi-chart-bar', to: '/menu/charts' },
            { label: 'User List', icon: 'pi pi-fw pi-building', to: '/menu/user-list' }
            
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
                to: '/pages/blood-inventory'
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
