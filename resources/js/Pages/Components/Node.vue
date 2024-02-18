<!-- Node.vue -->
<template>
    <div class="node" :style="nodeStyle"></div>
  </template>
  
  <script setup>
    const props = defineProps({
      isStart: Boolean,
      isEnd: Boolean,
      visited: Boolean,
      position: {
        type: Array,
        required: true
      }
    });


    const nodeStyle = {
        left: `${props.position[0] * 20}px`, 
        top: `${props.position[1] * 20}px`,
    };
  </script>

  
  
  <style scoped>
  .node {
    width: 3px;
    height: 3px; 
    background-color: blue; 
    border-radius: 50%;
    position: absolute;
  }
  
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
  