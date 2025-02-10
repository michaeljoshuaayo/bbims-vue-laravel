<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useLayout } from '@/layout/composables/layout';
import { useAuth } from 'vue-auth3';

const { onMenuToggle, toggleDarkMode, isDarkTheme } = useLayout();
const auth = useAuth();
const router = useRouter();
const showProfileDropdown = ref(false);
const user = ref(null);

const toggleProfileDropdown = () => {
    showProfileDropdown.value = !showProfileDropdown.value;
};

const handleLogout = async () => {
    try {
        await auth.logout();
        router.push('/login');
    } catch (error) {
        console.error('Error logging out:', error);
    }
};

const getUserData = async () => {
    try {
        const res = await auth.fetch();
        console.log('User data:', res.data);
        user.value = res.data[0];
    } catch (error) {
        console.error('Error fetching user data:', error);
    }
};

onMounted(() => {
    getUserData();
});
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="onMenuToggle">
                <i class="pi pi-bars"></i>
            </button>
            <router-link to="/" class="layout-topbar-logo">
                <span class="smaller-text">BBIMS</span>
            </router-link>
        </div>

        <div class="layout-topbar-actions">
            <div class="layout-config-menu">
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>
            </div>

            <button
                class="layout-topbar-menu-button layout-topbar-action"
                v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
            >
                <i class="pi pi-ellipsis-v"></i>
            </button>

            <div class="layout-topbar-menu hidden lg:block">
                <div class="layout-topbar-menu-content">
                    <button type="button" class="layout-topbar-action">
                        <i class="pi pi-bell"></i>
                        <span>Messages</span>
                    </button>
                    <div class="relative">
                        <button type="button" class="layout-topbar-action" @click="toggleProfileDropdown">
                            <i class="pi pi-user"></i>
                            <span>{{ user?.name || 'Profile' }}</span>
                        </button>
                        <div v-if="showProfileDropdown" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg">
                            <div class="px-4 py-2 border-b">
                                
                                <span>{{ user?.name }}</span>
                            </div>
                            <button type="button" class="w-full text-left px-4 py-2 hover:bg-gray-100" @click="handleLogout">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}

.layout-topbar-menu-content .relative {
    position: relative;
}

.layout-topbar-menu-content .absolute {
    position: absolute;
}
</style>
