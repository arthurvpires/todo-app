<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <header class="flex justify-between items-center mb-6 max-w-7xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-900">Gerenciar Usuários</h1>
            <div class="flex gap-4 items-center">
                <router-link to="/dashboard"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Dashboard
                </router-link>
                <button @click="logout" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    Logout
                </button>
            </div>
        </header>

        <section class="max-w-7xl mx-auto">
            <div class="bg-white rounded shadow p-4">
                <h2 class="text-xl font-semibold mb-4">Lista de Usuários</h2>
                <div v-if="users.length === 0" class="text-gray-500 text-center">
                    Nenhum usuário encontrado.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-left text-gray-700">
                                <th class="p-3">ID</th>
                                <th class="p-3">Nome</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Admin</th>
                                <th class="p-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id" class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ user.id }}</td>
                                <td class="p-3">{{ user.name }}</td>
                                <td class="p-3">{{ user.email }}</td>
                                <td class="p-3">{{ user.is_admin ? '✓' : '✕' }}</td>
                                <td class="p-3">
                                    <button v-if="!user.is_admin || user.id !== auth.user?.id" @click="deleteUser(user)"
                                        class="text-red-500 hover:text-red-700" title="Excluir Usuário">
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="showModal = true"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Criar Usuário
                    </button>
                </div>
            </div>

            <!-- Modal para criar usuário -->
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h3 class="text-xl font-semibold mb-4">Criar Novo Usuário</h3>
                    <form @submit.prevent="createUser">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-1" for="name">Nome</label>
                            <input v-model.trim="newUser.name" type="text" id="name" placeholder="Nome" required
                                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-1" for="email">Email</label>
                            <input v-model.trim="newUser.email" type="email" id="email" placeholder="Email" required
                                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-1" for="password">Senha</label>
                            <input v-model.trim="newUser.password" type="password" id="password" placeholder="Senha"
                                required minlength="6"
                                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div v-if="auth.user?.is_admin" class="mb-4">
                            <label class="block text-gray-700 mb-1" for="is_admin">Administrador</label>
                            <select v-model="newUser.is_admin" id="is_admin"
                                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option :value="false">Não</option>
                                <option :value="true">Sim</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-4">
                            <button type="button" @click="showModal = false"
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                Criar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import api from '@/lib/api';

const auth = useAuthStore();
const router = useRouter();
const users = ref([]);
const showModal = ref(false);
const newUser = ref({
    name: '',
    email: '',
    password: '',
    is_admin: false,
});

async function fetchUsers() {
    try {
        const response = await api.get('/api/users');
        users.value = response.data;
    } catch (error) {
        alert('Erro ao carregar usuários: ' + (error.response?.data.message || error.message));
    }
}

async function createUser() {
    if (!newUser.value.name || !newUser.value.email || !newUser.value.password) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
    }
    if (newUser.value.password.length < 6) {
        alert('A senha deve ter pelo menos 6 caracteres.');
        return;
    }

    try {
        const response = await api.post('/api/users/create', newUser.value);
        newUser.value = { name: '', email: '', password: '', is_admin: false };
        showModal.value = false;
        await fetchUsers();
    } catch (error) {
        alert('Erro ao criar usuário: ' + (error.response?.data.message || error.message));
    }
}

async function deleteUser(user) {
    if (!confirm(`Tem certeza que deseja excluir o usuário ${user.name}?`)) return;
    try {
        await api.delete(`/api/users/${user.id}`);
        await fetchUsers();
    } catch (error) {
        alert('Erro ao excluir usuário: ' + (error.response?.data.message || error.message));
    }
}

async function logout() {
    try {
        await api.post('/api/logout');
        auth.logout();
        router.push('/login');
    } catch (error) {
        alert('Erro no logout: ' + (error.response?.data.message || error.message));
    }
}

onMounted(() => {
    if (!auth.accessToken || !auth.user?.is_admin) {
        auth.logout();
        router.push('/login');
    } else {
        fetchUsers();
    }
});
</script>

<style scoped>
table {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

table::-webkit-scrollbar {
    width: 6px;
}

table::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}
</style>