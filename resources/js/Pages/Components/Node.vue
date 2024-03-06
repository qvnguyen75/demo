<!-- Node.vue -->
<template>
    <div :style="[nodeStyle]" :class="{start: node.start}"  @click="clickNode(node)">
      <p>{{ node.id }}</p>
      <!-- <p>{{ node.position_x }} {{ node.position_y }}</p> -->
    </div>
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
  