<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  initialValue: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['search']);
const searchQuery = ref(props.initialValue);
const searchInput = ref(null);

let debounceTimeout;

watch(searchQuery, (newValue) => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(() => {
    emit('search', newValue);
    setTimeout(() => {
      searchInput.value?.focus();
    }, 100);
  }, 300);
});
</script>

<template>
  <div class="mb-4">
    <input
      ref="searchInput"
      v-model="searchQuery"
      type="text"
      placeholder="Поиск по имени..."
      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      autofocus
    />
  </div>
</template>
