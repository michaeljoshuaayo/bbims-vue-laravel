<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { useAuth } from 'vue-auth3';

const auth = useAuth();

const email = ref('');
const password = ref('');
const checked = ref(false);
const toast = useToast();
const router = useRouter();

const handleLogin = async () => {
    auth.login({
        data: {
            email: email.value,
            password: password.value,
        },
        remember: checked.value,
    })
    .then(() => {
        toast.add({ severity: 'success', summary: 'Success', detail: 'Logged in successfully', life: 3000 });
        router.push('/');
    })
    .catch((error) => {
        const errorMessage = error.response?.data?.message || 'Failed to log in. Please check your credentials and try again.';
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessage });
        console.error('Error logging in:', error);
    });
};
</script>

<template>
    <FloatingConfigurator />
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="flex flex-col items-center justify-center">
                        <div class="logo">
                            <img src="/BBIMS.svg" alt="BBIMS Logo" />
                        </div>
                    </div>
                    <div class="text-center mb-8 mt-9">
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4"></div>
                        <span class="text-muted-color font-medium">Sign in to continue</span>
                    </div>
                    <div>
                        <label for="email1" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email</label>
                        <InputText id="email1" type="text" placeholder="Email address" class="w-full md:w-[30rem] mb-8" v-model="email" />

                        <label for="password1" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Password</label>
                        <Password id="password1" v-model="password" placeholder="Password" :toggleMask="true" class="mb-4" fluid :feedback="false"></Password>

                        <div class="flex items-center justify-between mt-2 mb-8 gap-8">
                            <div class="flex items-center">
                                <Checkbox v-model="checked" id="rememberme1" binary class="mr-2"></Checkbox>
                                <label for="rememberme1">Remember me</label>
                            </div>
                            <span class="font-medium no-underline ml-2 text-right cursor-pointer text-primary">Forgot password?</span>
                        </div>
                        <Button label="Sign In" class="w-full" @click="handleLogin"></Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Toast />
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
</style>
