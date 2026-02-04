<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border border-border bg-input-background shadow-sm focus:border-primary focus:ring-primary/50 focus:ring-2 text-sm"
        v-model="model"
        ref="input"
    />
</template>
