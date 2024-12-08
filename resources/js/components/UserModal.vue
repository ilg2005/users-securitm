<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    user: {
        type: Object,
        default: null
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const form = ref({
    id: props.user ? props.user.id : '',
    name: props.user ? props.user.name : '',
    email: props.user ? props.user.email : '',
    ip: props.user ? props.user.ip : '',
    comment: props.user ? props.user.comment : '',
    password: ''
});

const processing = ref(false);

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

// Функция для обработки успешного ответа
const handleSuccess = () => {
    emit('close');
    const searchParams = new URL(window.location.href).searchParams;
    window.location.href = getUrlWithParams({
        page: searchParams.get('page') || 1,
        sort_field: searchParams.get('sort_field') || 'id',
        sort_order: searchParams.get('sort_order') || 'asc',
        search: searchParams.get('search') || ''
    });
};

// Отправка формы
const submitForm = () => {
    processing.value = true;

    if (props.isEdit) {
        Inertia.post(`/users/${form.value.id}`, {
            ...form.value,
            _method: 'put'
        }, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: handleSuccess,
            onError: () => {
                processing.value = false;
            }
        });
    } else {
        // Создание нового пользователя
        Inertia.post(`/users`, {
            ...form.value
        }, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: handleSuccess,
            onError: () => {
                processing.value = false;
            }
        });
    }
};

// Удаление пользователя
const deleteUser = () => {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        processing.value = true;

        Inertia.post(`/users/${form.value.id}`, {
            _method: 'delete'
        }, {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: handleSuccess,
            onError: () => {
                processing.value = false;
            }
        });
    }
};
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full h-full md:max-w-xl md:h-auto">
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-xl font-semibold">
          {{ isEdit ? 'Редактирование пользователя' : 'Добавление пользователя' }}
        </h2>
        <button
          @click="emit('close')"
          class="text-gray-500 hover:text-gray-700 focus:outline-none"
          :disabled="processing"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <form @submit.prevent="submitForm" class="p-6 overflow-auto h-full">
        <div class="mb-4" v-if="isEdit">
          <label class="block text-gray-700">ID</label>
          <input type="text" v-model="form.id" disabled class="w-full px-3 py-2 border rounded bg-gray-100">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Имя</label>
          <input type="text" v-model="form.name" required class="w-full px-3 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Email</label>
          <input type="email" v-model="form.email" :disabled="isEdit" class="w-full px-3 py-2 border rounded" :class="{'bg-gray-100': isEdit}" :placeholder="isEdit ? 'Email нельзя изменить' : 'Введите Email'">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">IP</label>
          <input type="text" v-model="form.ip" class="w-full px-3 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Комментарий</label>
          <textarea v-model="form.comment" class="w-full px-3 py-2 border rounded"></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Пароль</label>
          <input type="password" v-model="form.password" class="w-full px-3 py-2 border rounded" :placeholder="isEdit ? 'Оставьте пустым, если не хотите менять' : 'Введите пароль'">
        </div>
        <div class="flex justify-between space-x-2">
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" :disabled="processing">
            {{ isEdit ? 'Сохранить' : 'Создать пользователя' }}
          </button>
          <button v-if="isEdit" type="button" @click="deleteUser" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" :disabled="processing">
            Удалить пользователя
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
