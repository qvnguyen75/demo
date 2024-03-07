<!-- Node.vue -->
<template>
    <div :style="[nodeStyle]" :class="{start: node.start, end: node.end}"  @click="clickNode(node)">{{ node.id }}</div>
  </template>
  
  <script setup>
  import { defineProps, ref, onMounted, watch, nextTick } from 'vue';

  const props = defineProps({
    isStart: Boolean,
    isEnd: Boolean,
    visited: Boolean,
    node: Object,
    cellSize: Number
  });

  const emit = defineEmits(['nodeClicked'])

  const nodeStyle = {
    left: `${props.node.position_x * props.cellSize}px`, 
    top: `${props.node.position_y * props.cellSize}px`,
  };
  
  const clickNode = (currentNode) => {
    emit('nodeClicked', currentNode)
  }

  </script>

  <style scoped>
  .start {
    background-color: green;
  }
  
  .end {
    background-color: red;
  }
  
  .visited {
    background-color: yellow;
  }
  </style>
  