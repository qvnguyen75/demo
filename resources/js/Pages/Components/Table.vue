<template>
    <div
    v-for="(table, index) in tables"
    :key="index"
    class="table"
    :class="{start: table.start, end: table.end}"
    :style="tableStyle(table)"
    @click="startDrag(table, $event)"
    >
        {{ table.name }}
        <br>
        {{ table.property }}
    </div>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { router } from '@inertiajs/vue3';

    const emit = defineEmits(['onTableMove', 'onTableClick']);

    const props = defineProps({
        tables: {
            type: Array
        },
        zoomLevel: {
            type: Number
        },
        cellSize: Number,
        nodes: Array

    })

    let correspondingTableNode = ref(Object);

    let tableSelected = false;

    const startDrag = (table, event) => {    
        const initialX = event.clientX - table.position_x;
        const initialY = event.clientY - table.position_y;

        const moveHandler = (event) => {
            if (tableSelected) {
                console.log(2.5);
                // Calculate the distance moved
                const dx = event.clientX - initialX;
                const dy = event.clientY - initialY;

                const snappedX = Math.round(dx / props.cellSize) * props.cellSize;
                const snappedY = Math.round(dy / props.cellSize) * props.cellSize;

                if (table.position_x !== snappedX || table.position_y !== snappedY) {
                    correspondingTableNode = props.nodes.find(node => node.position_x * props.cellSize === snappedX && node.position_y * props.cellSize === snappedY );

                    // update corresponding table node and emit to parent
                    table.node_id = correspondingTableNode.id;

                    emit('onTableMove', table);
                }

                table.position_x = snappedX;
                table.position_y = snappedY;
            }
        };

        if (tableSelected) {
            document.removeEventListener("mousemove", moveHandler);
            tableSelected = false;
            table.node_id = correspondingTableNode.id;
            router.put('/', table);
            return;
        }
        
        document.addEventListener("mousemove", moveHandler);
        
        table.start = true;
        tableSelected = true;   
    }

    const tableStyle = computed(() => {
        return (table) => ({
            left: table.position_x + 'px',
            top: table.position_y + 'px',
            transform: `scale(${props.zoomLevel})`
        });
    });
</script>

<style scoped>
.table {
  width: 200px;
  height: 200px;
  border: 1px solid #000;
  background-color: #f9f9f9;
  position: absolute;
  cursor: move;
}

.start {
    background-color: green;
}

.end {
    background-color: red;
  }
</style>