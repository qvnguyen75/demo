<template>
  <button @click="createTable" class="btn">Create table</button>
  <button @click="toggleNodes" class="btn">Toggle nodes</button>
  <button @click="toggleGrid" class="btn">Toggle grid</button>

  <div>
    <Modal v-if="showModal" :showModal="showModal" @tableSaved="handleTableSaved" @modalClosed="handleModalClosed" />
    <div id="diagram-container" ref="container" @wheel.prevent="handleZoom">
      <div class="grid" :style="gridStyle"></div>
      <div class="grid-container">
        <Node v-if="showNodes" v-for="(node, index) in nodes" :key="index" :node="node" :cellSize="cellSize" class="node" @nodeClicked="handleNodeClick" />
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
  import { Item } from './script.js';

  const showModal = ref(false);
  const tables    = ref([]);
  const zoomLevel = ref(1); // startwaarde
  const cellSize  = ref(100);
  // const maxNodes  = ref(25);

  const showNodes = ref(true)
  const showGrid = ref(true)

  const props = defineProps({
    entity: Object
  })

  const nodes= ref([])

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
    // columns
    for (let i = 0; i <= 3; i++) {
      // rows
      for (let j = 0; j <= 3; j++) {
        const node = new Item(i, j);
        node.id = id;
        nodes.value.push(node)
        id++;
      }
    } 
  }

  const handleNodeClick = (currentNode) => {
    let firstNodeSet = nodes.value.find((currentNode) => currentNode.start === true);
    let endNodeSet = nodes.value.find((currentNode) => currentNode.end === true);

    if (firstNodeSet) {
      if (endNodeSet === undefined){
        currentNode.end = true;
        console.log('Marked the current node as end point!')
        console.log('Position = ' + currentNode.position_x + ' ' + currentNode.position_y)
      }
      return;
    }

    if (firstNodeSet === undefined) {
      currentNode.start = true;
      console.log('Marked the current node as starting point!')
      console.log('Position = ' + currentNode.position_x + ' ' + currentNode.position_y)
    }
  }

  nextTick(() => {
          for (let i = 0; i < nodes.value.length; i++) {
          let node = nodes.value[i];

          let positionX = node.position_x * cellSize.value;
          let positionY = node.position_y * cellSize.value;
        
          let nextNode = nodes.value[i + 1]
          let neighbourNode = nodes.value[i + (3 + 1)]; // vieze hek needs fix

          if (nextNode == undefined) { return }

          if (node.position_x === nextNode.position_x) {
            let nextNodePosition_x = nextNode.position_x * cellSize.value;
            let nextNodePosition_y = nextNode.position_y * cellSize.value;
            drawLine(positionX, positionY, nextNodePosition_x, nextNodePosition_y);
          }

          if (neighbourNode) {
            let neighbourNodePosition_x = neighbourNode.position_x * cellSize.value;
            let neighbourNodePposition_y = neighbourNode.position_y * cellSize.value;

            drawLine(positionX, positionY, neighbourNodePosition_x, neighbourNodePposition_y)
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
  height: 94.6vh;
  border: 1px solid #ccc;
  position: relative;
  overflow: hidden;
}

.btn {
  margin-bottom: 10px;
  margin-right: 10px;
}

.node {
    width: 100px;
    height: 100px; 
    /* background-color: blue;  */
    border-radius: 50%;
    position: absolute;
  }

</style>

