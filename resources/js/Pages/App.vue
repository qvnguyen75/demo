<template>
  <button @click="createTable" class="btn">Create table</button>
  <button @click="toggleNodes" class="btn">Toggle nodes</button>
  <button @click="toggleGrid" class="btn">Toggle grid</button>

  <div>
    <Modal v-if="showModal" :showModal="showModal" @tableSaved="handleTableSaved" @modalClosed="handleModalClosed" />
    <div id="diagram-container" ref="container" @wheel.prevent="handleZoom">
      <div class="grid" :style="gridStyle"></div>
      <div class="grid-container">
        <!-- <Node v-for="(cell, index) in grid" :key="index" :cell="cell" :position="cell.position" class="node" /> -->
        <Node v-if="showNodes" v-for="(node, index) in nodes" :key="index" :node="node" :cellSize="cellSize" class="node"/>
      </div>
      <Table :tables="tables" :zoomLevel="zoomLevel" :cellSize="cellSize" />
      <svg v-if="showGrid" id="lines" width="10000" height="10000" xmlns="http://www.w3.org/2000/svg"></svg>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, nextTick  } from 'vue';

  import Modal from './Components/Modal.vue';
  import Table from './Components/Table.vue';
  import Node from './Components/Node.vue';

  const showModal = ref(false);
  const tables    = ref([]);
  const zoomLevel = ref(1); // startwaarde
  const cellSize  = ref(80);
  // const maxNodes  = ref(25);

  const nodes = ref([]);
  const showNodes = ref(true)
  const showGrid = ref(true)

  const createTable = () => {
    showModal.value = true;
  }

  const toggleNodes = () => {
    showNodes.value = !showNodes.value
  }

  const toggleGrid = () => {
    showGrid.value = !showGrid.value
  }

  const createNodes = () => {
    let id = 0;
    for (let i = 0; i <= 100; i++) {
      for (let j = 0; j <= 100; j++) {
        const node = {
            id: id,
            position: {positionX:i, positionY: j},
            // nextNode: {
            //   positionX: i,
            //   positionY: j + 1
            // }
        }

        id++;
        nodes.value.push(node);
      }
    } 
  }

  nextTick(() => {
      for (let i = 0; i < nodes.value.length; i++) {
          let node = nodes.value[i];

          let positionX = node.position.positionX * cellSize.value;
          let positionY = node.position.positionY * cellSize.value;
        
          let nextNode = nodes.value[i + 1]
          let neighbourNode = nodes.value[i + 101];

          if (nextNode == undefined) { return }

          if (node.position.positionX === nextNode.position.positionX) {
            
            let nextNodePositionX = nextNode.position.positionX * cellSize.value;
            let nextNodePositionY = nextNode.position.positionY * cellSize.value;
            
            drawLine(positionX, positionY, nextNodePositionX, nextNodePositionY)
          }

          if (neighbourNode) {
            let neighbourNode_position_x = neighbourNode.position.positionX * cellSize.value;
            let neighbourNode_position_y = neighbourNode.position.positionY * cellSize.value;

            drawLine(positionX, positionY, neighbourNode_position_x, neighbourNode_position_y)
          }
      }
  });

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

  const drawLine = (x1, y1, x2, y2) => {
      const svg = document.getElementById('lines');
      const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
      line.setAttribute("x1", x1);
      line.setAttribute("y1", y1);
      line.setAttribute("x2", x2);
      line.setAttribute("y2", y2);
      line.setAttribute("stroke", "black");
      line.setAttribute("stroke-width", 1);
      svg.appendChild(line);
  }

  onMounted(() => {
    createNodes();
  })
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

.btn {
  margin-bottom: 10px;
  margin-right: 10px;
}

.node {
    width: 5px;
    height: 5px; 
    background-color: blue; 
    border-radius: 50%;
    position: absolute;
  }

</style>

