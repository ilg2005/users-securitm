<script setup>
import { ref, computed } from 'vue';
import Loader from '@/components/Loader.vue';
import Pagination from '@/components/Pagination.vue';
import SearchInput from '@/components/SearchInput.vue';
import UserModal from '@/components/UserModal.vue';
import { usePage } from '@inertiajs/inertia-vue3';

const page = usePage();
const users = computed(() => page.props.value.users.data);
const current_page = computed(() => page.props.value.users.current_page);
const last_page = computed(() => page.props.value.users.last_page);
const loading = ref(false);

// Состояние сортировки и поиска
const sortField = ref(page.props.value.sort.field || 'id');
const sortOrder = ref(page.props.value.sort.order || 'asc');
const search = ref(page.props.value.search || '');

const showModal = ref(false);
const isEditMode = ref(false); // Флаг режима редактирования
const selectedUser = ref(null);

const openModal = (user = null) => {
    selectedUser.value = user;
    isEditMode.value = user !== null;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedUser.value = null;
    isEditMode.value = false;
};

// Функция для получения URL с параметрами
const getUrlWithParams = (params) => {
    const url = new URL(window.location.href);
    Object.entries(params).forEach(([key, value]) => {
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
    });
    return url.toString();
};

// Обработчик изменения страницы
const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= last_page.value) {
        loading.value = true;
        window.location.href = getUrlWithParams({
            page: newPage,
            sort_field: sortField.value,
            sort_order: sortOrder.value,
            search: search.value
        });
    }
};

// Обработчик сортировки
const handleSort = (field) => {
    let newOrder = 'asc';

    if (sortField.value === field) {
        // Если кликнули по тому же полю - меняем направление
        newOrder = sortOrder.value === 'asc' ? 'desc' : 'asc';
    }

    loading.value = true;
    window.location.href = getUrlWithParams({
        sort_field: field,
        sort_order: newOrder,
        page: 1,
        search: search.value
    });
};

// Обработчик поиска
const handleSearch = (query) => {
    loading.value = true;
    window.location.href = getUrlWithParams({
        search: query,
        page: 1,
        sort_field: sortField.value,
        sort_order: sortOrder.value
    });
};

// Вычисляемое свойство для иконки сортировки
const getSortIcon = (field) => {
    if (sortField.value !== field) return '';
    return sortOrder.value === 'asc' ? '↑' : '↓';
};
</script>

<template>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">

        <h1 class="mb-6 text-4xl font-bold text-blue-500">
            {{ loading ? 'Загружаем...' : 'Список пользователей' }}
        </h1>


        <div class="w-full max-w-6xl relative">
            <div class="flex justify-between items-center mb-4">
                <!-- Поиск -->
                <SearchInput
                    :initial-value="search"
                    @search="handleSearch"
                />
                <button
                    @click="openModal()"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                >
                    <span class="hidden md:inline">Новый пользователь</span>
                    <span class="md:hidden">+</span>
                </button>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th @click="handleSort('id')"
                                class="table-header cursor-pointer hover:text-blue-600">
                                ID {{ getSortIcon('id') }}
                            </th>
                            <th @click="handleSort('name')"
                                class="table-header cursor-pointer hover:text-blue-600">
                                Имя {{ getSortIcon('name') }}
                            </th>
                            <th class="table-header">Email</th>
                            <th class="table-header hidden md:table-cell">IP</th>
                            <th class="table-header hidden md:table-cell">Комментарий</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                          v-for="(user, index) in users"
                          :key="user.id"
                          class="hover:bg-gray-100 cursor-pointer"
                          :class="{ 'bg-gray-50': index % 2 === 0 }"
                          @click="openModal(user)"
                        >
                            <td class="table-cell">{{ user.id }}</td>
                            <td class="table-cell">{{ user.name }}</td>
                            <td class="table-cell">{{ user.email }}</td>
                            <td class="hidden md:table-cell">{{ user.ip }}</td>
                            <td class="hidden md:table-cell">{{ user.comment }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="loading"
                class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-50">
                <Loader />
            </div>

            <Pagination
                :current-page="current_page"
                :last-page="last_page"
                @change="handlePageChange"
            />
        </div>

        <UserModal v-if="showModal" :user="selectedUser" :is-edit="isEditMode" @close="closeModal" />
    </div>
</template>





