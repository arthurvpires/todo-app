<!-- Login.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-md">
      <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Login</h1>
      <form @submit.prevent="submitLogin">
        <div class="mb-4">
          <label class="block text-gray-700 mb-1" for="email">Email</label>
          <input v-model="form.email" id="email" type="email"
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div class="mb-6">
          <label class="block text-gray-700 mb-1" for="password">Password</label>
          <input v-model="form.password" id="password" type="password"
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required />
        </div>
        <button type="submit"
          class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
          Entrar
        </button>
        <p v-if="error" class="mt-4 text-center text-red-600">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import api from '@/lib/api';

const router = useRouter();
const auth = useAuthStore();

const form = ref({
  email: '',
  password: '',
});

const error = ref('');

async function submitLogin() {
  error.value = '';
  try {
    await api.get('/sanctum/csrf-cookie');
    const response = await api.post('/api/login', form.value);
    auth.setAuth(response.data);
    router.push('/dashboard');
  } catch (error) {
    error.value = error.response?.data?.message || 'Erro ao fazer login. Verifique suas credenciais.';
  }
}
</script>