<template>
    <div class="min-h-screen bg-gray-100 p-6">
        <header class="flex justify-between items-center mb-6 max-w-7xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-900">Olá, {{ auth.user?.name }}</h1>
            <div class="flex gap-4 items-center">
                <router-link v-if="auth.user?.is_admin" to="/users"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Gerenciar Usuários
                </router-link>
                <button @click="logout" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    Logout
                </button>
            </div>
        </header>

        <section class="max-w-7xl mx-auto">
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input v-model="searchQuery" type="text" placeholder="Pesquisar por título ou descrição"
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <button @click="exportToCSV"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Exportar para CSV
                </button>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                <form @submit.prevent="addTask" class="bg-white rounded shadow p-4 mb-6 md:mb-0 md:w-1/4">
                    <h2 class="text-xl font-semibold mb-4">Nova Tarefa</h2>
                    <input v-model="newTask.title" type="text" placeholder="Título" required
                        class="w-full mb-3 p-2 border rounded" />
                    <textarea v-model="newTask.description" placeholder="Descrição"
                        class="w-full mb-3 p-2 border rounded" rows="4"></textarea>
                    <input v-model="newTask.due_date" type="date" required class="w-full mb-3 p-2 border rounded" />
                    <select v-model="newTask.status" class="w-full mb-3 p-2 border rounded">
                        <option value="pending">Pendente</option>
                        <option value="in_progress">Em Progresso</option>
                        <option value="completed">Concluída</option>
                    </select>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                        Adicionar
                    </button>
                </form>

                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded shadow p-4 flex flex-col">
                        <h3 class="text-lg font-bold mb-4">Pendentes</h3>
                        <ul class="flex flex-col gap-3 overflow-y-auto max-h-[60vh]">
                            <li v-for="task in filteredTasks('pending')" :key="task.id"
                                class="p-3 border rounded hover:bg-gray-50 cursor-pointer" @click="openEditModal(task)">
                                <div class="flex justify-between items-center mb-1">
                                    <strong>{{ task.title }}</strong>
                                    <button @click.stop="deleteTask(task)" title="Excluir"
                                        class="text-red-500 hover:text-red-700">
                                        ✕
                                    </button>
                                </div>
                                <p v-if="task.description" class="text-gray-600 mb-2">{{ task.description }}</p>
                                <small class="text-gray-500">Vence: {{ formatDate(task.due_date) }}</small>
                                <div class="mt-2 flex gap-2">
                                    <button @click.stop="updateStatus(task, 'in_progress')"
                                        class="text-yellow-500 hover:text-yellow-700 font-semibold"
                                        title="Mover para Em Progresso">
                                        →
                                    </button>
                                    <button @click.stop="updateStatus(task, 'completed')"
                                        class="text-green-500 hover:text-green-700 font-semibold"
                                        title="Marcar como Concluída">
                                        ✓
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded shadow p-4 flex flex-col">
                        <h3 class="text-lg font-bold mb-4">Em Progresso</h3>
                        <ul class="flex flex-col gap-3 overflow-y-auto max-h-[60vh]">
                            <li v-for="task in filteredTasks('in_progress')" :key="task.id"
                                class="p-3 border rounded bg-blue-50 hover:bg-gray-50 cursor-pointer"
                                @click="openEditModal(task)">
                                <div class="flex justify-between items-center mb-1">
                                    <strong>{{ task.title }}</strong>
                                    <button @click.stop="deleteTask(task)" title="Excluir"
                                        class="text-red-500 hover:text-red-700">
                                        ✕
                                    </button>
                                </div>
                                <p v-if="task.description" class="text-gray-600 mb-2">{{ task.description }}</p>
                                <small v-if="task.due_date" class="text-gray-500">Vence: {{ formatDate(task.due_date)
                                }}</small>
                                <div class="mt-2 flex gap-2">
                                    <button @click.stop="updateStatus(task, 'pending')"
                                        class="text-blue-500 hover:text-blue-700 font-semibold"
                                        title="Voltar para Pendentes">
                                        ←
                                    </button>
                                    <button @click.stop="updateStatus(task, 'completed')"
                                        class="text-green-500 hover:text-green-700 font-semibold"
                                        title="Marcar como Concluída">
                                        ✓
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded shadow p-4 flex flex-col">
                        <h3 class="text-lg font-bold mb-4">Concluídas</h3>
                        <ul class="flex flex-col gap-3 overflow-y-auto max-h-[60vh]">
                            <li v-for="task in filteredTasks('completed')" :key="task.id"
                                class="p-3 border rounded bg-green-50 cursor-pointer" @click="openEditModal(task)">
                                <div class="flex justify-between items-center mb-1">
                                    <strong class="line-through">{{ task.title }}</strong>
                                    <button @click.stop="deleteTask(task)" title="Excluir"
                                        class="text-red-500 hover:text-red-700">
                                        ✕
                                    </button>
                                </div>
                                <p v-if="task.description" class="text-gray-600 mb-2">{{ task.description }}</p>
                                <small v-if="task.due_date" class="text-green-700">Venceu: {{
                                    formatDate(task.due_date) }}</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div v-if="showEditModal"
                class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded shadow p-6 w-full max-w-md">
                    <h2 class="text-xl font-semibold mb-4">Editar Tarefa</h2>
                    <form @submit.prevent="saveEditedTask">
                        <input v-model="editTask.title" type="text" placeholder="Título" required
                            class="w-full mb-3 p-2 border rounded" />
                        <textarea v-model="editTask.description" placeholder="Descrição"
                            class="w-full mb-3 p-2 border rounded" rows="4"></textarea>
                        <input v-model="editTask.due_date" type="date" required
                            class="w-full mb-3 p-2 border rounded" />
                        <select v-model="editTask.status" class="w-full mb-3 p-2 border rounded">
                            <option value="pending">Pendente</option>
                            <option value="in_progress">Em Progresso</option>
                            <option value="completed">Concluída</option>
                        </select>
                        <div class="flex gap-4">
                            <button @click="closeEditModal"
                                class="flex-1 bg-gray-600 text-white py-2 rounded hover:bg-gray-700 transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                                Salvar
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
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import api from '@/lib/api';

const auth = useAuthStore();
const router = useRouter();

const newTask = ref({
    user_id: auth.user?.id,
    title: '',
    description: '',
    due_date: '',
    status: 'pending',
});

const tasks = ref([]);
const searchQuery = ref('');
const showEditModal = ref(false);
const editTask = ref({});

const filteredTasks = (status) => {
    const query = searchQuery.value.toLowerCase().trim();
    const filtered = tasks.value.filter((task) => {
        const matchesStatus = task.status === status;
        if (!query) return matchesStatus;
        const matchesTitle = task.title?.toLowerCase().includes(query);
        const matchesDescription = task.description?.toLowerCase().includes(query) || false;
        return matchesStatus && (matchesTitle || matchesDescription);
    });
    return filtered;
};

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
}

function escapeCSV(str) {
    if (!str) return '';
    const escaped = str.replace(/"/g, '""');
    return `"${escaped}"`;
}

function exportToCSV() {
    const tasksToExport = searchQuery.value
        ? [
            ...filteredTasks('pending'),
            ...filteredTasks('in_progress'),
            ...filteredTasks('completed'),
        ]
        : tasks.value;

    if (tasksToExport.length === 0) {
        alert('Nenhuma tarefa para exportar.');
        return;
    }

    const headers = ['ID,Título,Descrição,Data de Vencimento,Status'];
    const rows = tasksToExport.map((task) => {
        const formattedDate = formatDate(task.due_date);
        return `${task.id},${escapeCSV(task.title)},${escapeCSV(task.description)},${formattedDate},${task.status}`;
    });

    const csvContent = [...headers, ...rows].join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', 'tarefas.csv');

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

async function fetchTasks() {
    try {
        const response = await api.get('/api/tasks');
        tasks.value = response.data;
    } catch (error) {
        if (error.response?.status === 401) {
            auth.logout();
            router.push('/login');
        } else {
            alert('Erro ao carregar tarefas: ' + (error.response?.data.message || error.message));
        }
    }
}

async function addTask() {
    try {
        await api.post('/api/tasks', newTask.value);
        newTask.value.title = '';
        newTask.value.description = '';
        newTask.value.due_date = '';
        await fetchTasks();
    } catch (error) {
        alert('Erro ao adicionar tarefa: ' + (error.response?.data.message || error.message));
    }
}

async function updateStatus(task, status) {
    try {
        await api.put(`/api/tasks/${task.id}`, { ...task, status });
        await fetchTasks();
    } catch (error) {
        alert('Erro ao atualizar status: ' + (error.response?.data.message || error.message));
    }
}

async function deleteTask(task) {
    try {
        await api.delete(`/api/tasks/${task.id}`);
        await fetchTasks();
    } catch (error) {
        alert('Erro ao excluir tarefa: ' + (error.response?.data.message || error.message));
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

function openEditModal(task) {
    editTask.value = { ...task };
    showEditModal.value = true;
}

async function saveEditedTask() {
    try {
        await api.put(`/api/tasks/${editTask.value.id}`, editTask.value);
        await fetchTasks();
        closeEditModal();
    } catch (error) {
        alert('Erro ao editar tarefa: ' + (error.response?.data.message || error.message));
    }
}

function closeEditModal() {
    showEditModal.value = false;
    editTask.value = {};
}

onMounted(() => {
    if (!auth.accessToken) {
        auth.logout();
        router.push('/login');
    } else {
        fetchTasks();
    }
});
</script>

<style scoped>
ul {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

ul::-webkit-scrollbar {
    width: 6px;
}

ul::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}
</style>