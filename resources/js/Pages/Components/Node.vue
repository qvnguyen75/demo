<!-- Node.vue -->
<template>
    <div class="node" :style="nodeStyle"></div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
    const props = defineProps({
      isStart: Boolean,
      isEnd: Boolean,
      visited: Boolean,
      node: {
          id: Number,
          position: {
          type: Array,
          required: true,
          nextNode: Object
        }
      },
      cellSize: Number
   
    });

    const nodeStyle = {
        left: `${props.node.position.positionX * props.cellSize}px`, 
        top: `${props.node.position.positionY * props.cellSize}px`,
    };

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
  