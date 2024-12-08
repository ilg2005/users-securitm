<script setup>
import { ref } from 'vue';

const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  lastPage: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['change']);
const currentPage = ref(props.currentPage);

</script>
<template>
  <div class="flex justify-center mt-4 gap-2">
    <button
      @click="$emit('change', --currentPage)"
      :disabled="currentPage <= 1"
      class="px-4 py-2 bg-white border rounded hover:bg-gray-50 disabled:opacity-50"
    >
      Назад
    </button>

    <div class="px-4 py-2 bg-white border rounded">
      {{ currentPage }} из {{ lastPage }}
    </div>

    <button
      @click="$emit('change', ++currentPage)"
      :disabled="currentPage >= lastPage"
      class="px-4 py-2 bg-white border rounded hover:bg-gray-50 disabled:opacity-50"
    >
      Вперед
    </button>
  </div>
</template>


