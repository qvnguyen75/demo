<template>
  <button @click="createTable">Create table</button>
  <div>
    <Modal v-if="showModal" :showModal="showModal" @tableSaved="handleTableSaved" @modalClosed="handleModalClosed" />
    
    <div id="diagram-container" ref="container" @wheel.prevent="handleZoom">
      <div class="grid" :style="gridStyle"></div>
      <div class="grid-container">
        <Node v-for="(cell, index) in grid" :key="index" :position="cell.position" class="node" />
      </div>
      <Table :tables="tables" :zoomLevel="zoomLevel" />
      <svg viewBox="0 0 100 100" id="mySvg"></svg>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue';
  import Modal from './Components/Modal.vue';
  import Table from './Components/Table.vue';
  import Node from './Components/Node.vue';

  const showModal = ref(false);
  const tables    = ref([]);
  const zoomLevel = ref(1); // startwaarde
  const cellSize  = 20; 

  const grid = ref([]);
  const nodes = ref([]);



  const createTable = () => {
    showModal.value = true;
  }

  
  onMounted(() => {
    const test = document.getElementById("diagram-container");
    const rowSize = test.clientHeight / cellSize;
    const colSize = test.clientWidth / cellSize;

    let id = 0;
    for (let i = 0; i <= colSize; i++) {
      for (let j = 0; j <= rowSize; j++) {
      
        grid.value.push({position: [i, j]});
         const node = {
            position: {positionX:i, positionY: j},
            nextNode: {
              positionX: i,
              positionY: j + 1
            }
        }
        nodes.value.push(node);
      }
    }
    // drawLine(nodes.value, "black", .1);    
  })

  const handleTableSaved = (table) => {
    if (table.tableName && table.property) {
      const container = document.getElementById("diagram-container");
      const posX      = Math.random() * (container.offsetWidth - 120);
      const posY      = Math.random() * (container.offsetHeight - 80);

      tables.value.push({ name: table.tableName, property: table.property, x: posX, y: posY });

      showModal.value = false;       
    }
  }

  const handleZoom = (event) => {
    const delta   = Math.sign(event.deltaY);
    const step    = 0.1;
    const maxZoom = 3; 
    const minZoom = 1; 

    zoomLevel.value = Math.min(maxZoom, Math.max(minZoom, zoomLevel.value + delta * step));
  }

  const gridStyle = computed(() => ({
    transform: `scale(${zoomLevel.value})`,
  }));

  const handleModalClosed = (waarde) => {
    if (waarde) {
      showModal.value = false;
    }
  }

  const prepareLines = (start, eind) => {

  }

  const drawLine = (nodes, strokeColor, strokeWidth) => {
    for (let i = 0; i <= nodes.length; i++) {

    }
    const svg = document.getElementById('mySvg');
    const svgNamespace = "http://www.w3.org/2000/svg";
    console.log(nodes[0].position.positionX, nodes[0].nextNode);
    nodes.forEach((node) => {
      console.log(node)
      // const line = document.createElementNS(svgNamespace, "line");
      // console.log(node.position.positionX);
      // console.log(node.position.positionY);
      // console.log(node.nextNode.positionX);
      // console.log(node.nextNode.positionX);
      
      // line.setAttribute("x1", node.position.positionX);
      // line.setAttribute("y1", node.position.positionY);
      // line.setAttribute("x2", node.nextNode.positionX);
      // line.setAttribute("y2", node.nextNode.positionY);
      // line.setAttribute("stroke", strokeColor);
      // line.setAttribute("stroke-width", strokeWidth);
      // svg.appendChild(line)
    }) 


    // drawLine(startNode.nextNode, 'black', .1)
  }

  

  // drawLine()

</script>

<style scoped>
#diagram-container {
  width: 99vw;
  height: 95vh;
  border: 1px solid #ccc;
  position: relative;
  overflow: hidden;
}

/* .grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, 20px); 
  grid-template-rows: repeat(auto-fill, 20px);
} */

/* .grid {
  position: absolute;
  width: 100%;
  height: 100%;
  background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.2) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 0, 0, 0.2) 1px, transparent 1px);
  background-size: 20px 20px;
} */

.lines {
  position: absolute;
  pointer-events: none; /* This ensures that the SVG does not capture mouse events */
}
</style>

