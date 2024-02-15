<template>
<p>{{ test }}</p>
</template>

<script setup>
defineProps({
    test: {
        type: Number
    }
})
</script>