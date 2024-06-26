<template>
  <button @click="createTable" class="btn">Create table</button>
  <button @click="toggleNodes" class="btn">Toggle nodes</button>
  <button @click="toggleGrid" class="btn">Toggle grid</button>

  <div>
    <Modal v-if="showModal" :showModal="showModal" @tableSaved="handleTableSaved" @modalClosed="handleModalClosed" />
    <div id="diagram-container" ref="container" @wheel.prevent="handleZoom">
      <div class="grid" :style="gridStyle"></div>
      <div class="grid-container">
          <Node v-if="showNodes"
            v-for="(node, index) in nodes" :key="index" 
            :node="node" 
            :cellSize="cellSize" 
            class="node" 
            @nodeClicked="handleNodeClick"
          />
          <Table :tables="tables"
            :nodes="nodes"
            @onTableMove="onTableMove"
            :zoomLevel="zoomLevel"
            :cellSize="cellSize" />
      </div>
      <svg v-if="showGrid" id="lines" width="10000" height="10000" xmlns="http://www.w3.org/2000/svg"></svg>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, nextTick } from 'vue';
  import { router } from '@inertiajs/vue3';

  import Modal from './Components/Modal.vue';
  import Table from './Components/Table.vue';
  import Node from './Components/Node.vue';
  import { Item } from './script.js';

  const zoomLevel = ref(1); // startwaarde
  const cellSize  = ref(25);
  const colSize   = ref(76);
  const rowSize   = ref(35);
  
  // initialize as 0
  const numberOfNodes = 0;
  
  // const maxNodes  = ref(25);

  const showNodes = ref(true);
  const showModal = ref(false);
  const showGrid = ref(true);

  const props = defineProps({
    tables: Array
  });

  const tables = ref(props.tables);
  const nodes  = ref([]);

  let tableStartNodes = [];
  let tableEndNodes = [];
  let pathList = [];
  let startNode = null;
  let endNode = null;
 
  
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
    for (let i = 0; i <= colSize.value; i++) {
      // rows
      for (let j = 0; j <= rowSize.value; j++) {
        const node = new Item(i, j);
        node.id = id;
        nodes.value.push(node)
        id++;
      }
    } 
  }

  const onTableMove = (table) => {
    // always index - 1 to get the node above the table
    let correspondingTableNode = nodes.value[table.node_id - 1];

    if (tableStartNodes.length > 0) {
      let previousStartNodeId = tableStartNodes[0].id;
      let previousStartNode   = nodes.value[previousStartNodeId];

      if (table.start) {
        previousStartNode.start = false;
        tableStartNodes.shift();
        startNode = correspondingTableNode;
      }
      
    }

    if (tableEndNodes.length > 0) {
      let previousStartNodeId = tableEndNodes[0].id;
      let previousStartNode   = nodes.value[previousStartNodeId];

      if (table.end) {
        previousStartNode.end = false;
        tableEndNodes.shift();
        endNode = correspondingTableNode;
      }
    }
    
    if (table.start) {
      tableStartNodes.push(correspondingTableNode);
      correspondingTableNode.start = true;
      if (startNode, endNode) {
        bfs(startNode, endNode);
      }
    }

    if (table.end) {
      tableEndNodes.push(correspondingTableNode);
      correspondingTableNode.end = true;

      bfs(startNode, endNode);
    }
  }

  const handleNodeClick = (currentNode) => {
    // if startnode is set 
    if (currentNode.start) {
      console.log('current node is the starting node');
      return;
    }

    console.log('node visited = ' + currentNode.visited + ' . Startnode = ' + currentNode.start + ' . Endnode = ' + currentNode.end)
    let startNode = nodes.value.find((currentNode) => currentNode.start === true);
    let endNode   = nodes.value.find((currentNode) => currentNode.end === true);

    if (startNode === undefined) {
      currentNode.start = true;
      console.log('Marked the current node as starting point!')
      console.log('Position = ' + currentNode.position_x + ' ' + currentNode.position_y)
    }

    if (startNode) {
      if (endNode === undefined){
        currentNode.end = true;
        console.log('Marked the current node as end point!');
        console.log('Position = ' + currentNode.position_x + ' ' + currentNode.position_y);

        let endNode = currentNode;

        // breadth first search
        bfs(startNode, endNode);
      }
    }
  }

  nextTick(() => {
          for (let i = 0; i < nodes.value.length; i++) {
          let node = nodes.value[i];

          let positionX = node.position_x * cellSize.value;
          let positionY = node.position_y * cellSize.value;
        
          let nextNode = nodes.value[i + 1]
          
          let neighbourNode = nodes.value[i + (rowSize.value + 1)]; // vieze hek needs fix

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
      // random position on grid
      const nodeId = Math.round(Math.random() * (nodes.value.length / 2));

      const node = nodes.value[nodeId];

      table.node_id    = node.id;
      table.position_x = node.position_x * cellSize.value;
      table.position_y = node.position_y * cellSize.value;
      table.start      = false;

      tables.value.push(table);

      router.post('/', table);

      showModal.value = false;       
    }
  }

  const bfs = (startNode, endNode) => {
    let prev = solve(startNode, endNode);

    reconstructPath(startNode, endNode, prev);
  }

  const solve = (startNode, endNode) => {
    let queue = [];
    queue.push(startNode);

    let visited = new Array(nodes.value.length).fill(false);
    const prev = new Array(nodes.value.length).fill(null);
   
    while (queue.length > 0) {
      let node = queue.shift();

      if (node === endNode) { break };

      let siblings = returnNeighbours(node);

      for (let j = 0; j < siblings.length; j++) {
        let sibling = siblings[j];
        if (!visited[sibling.id]) {
          queue.push(sibling);
          visited[sibling.id] = true;
          prev[sibling.id] = node;
        }
      }
    }

    return prev;
  }

  const returnNeighbours = (currentNode) => {
    let neighbours        = [];
    let childNodeRightId  = currentNode.id + (rowSize.value + 1);
    let childNodeLeftId   = currentNode.id - (rowSize.value + 1);
    let childNodeTopId    = currentNode.id - 1;
    let childNodeBottomId = currentNode.id + 1;

    if (nodes.value[childNodeRightId] && nodes.value[childNodeRightId].position_x <= colSize.value) {
      neighbours.push(nodes.value[childNodeRightId]);
    }
    
    if (nodes.value[childNodeLeftId] && nodes.value[childNodeLeftId].position_x >= 0) {
      neighbours.push(nodes.value[childNodeLeftId])
    }

    if (nodes.value[childNodeBottomId] && nodes.value[childNodeBottomId].position_x == currentNode.position_x) {
      neighbours.push(nodes.value[childNodeBottomId]);
    }
    
    if (nodes.value[childNodeTopId] && nodes.value[childNodeTopId].position_x == currentNode.position_x) {
      neighbours.push(nodes.value[childNodeTopId])
    }
  
    return neighbours;
  }

  const reconstructPath = (startNode, endNode, prev) => {
    let path = [];

    if (pathList.length > 0) {
    // unset last path      
      for (let i = 0; i < pathList[0].length; i++) {
        pathList[0][i].visited = false;
      }
    }
    // and update new path
    let currentNode = endNode;

    while (currentNode !== null && currentNode !== startNode) {
      path.unshift(currentNode);
      currentNode = prev[currentNode.id];
    }

    if (currentNode === startNode) {
      path.unshift(startNode);
    } else {
      console.log("No path found");
    }

    for (let i = 0; i < path.length; i++) {
      path[i].visited = true;
    }

    pathList.unshift(path);
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
    width: 25px;
    height: 25px; 
    /* background-color: blue;  */
    /* border-radius: 50%; */
    position: absolute;
  }

</style>
