<template>
    <div
    v-for="(table, index) in tables"
    :key="index"
    class="table"
    :style="tableStyle(table)"
    draggable="true"
    @click="startDrag(index, $event)"
    >
        {{ table.name }}
        <br>
        {{ table.property }}
    </div>
</template>

<script setup>
    import { defineProps, computed } from 'vue';

    const props = defineProps({
        tables: {
            type: Array
        },
        zoomLevel: {
            type: Number
        },
        cellSize: Number
    })

    let selected = false;

    const startDrag = (index, event) => {
        const table = props.tables[index];
        const initialX = event.clientX - table.position_x;
        const initialY = event.clientY - table.position_y;

        const moveHandler = (event) => {
            if (selected) {
                // Calculate the distance moved
            const dx = event.clientX - initialX;
            const dy = event.clientY - initialY;

            // const gridSize = 50;
            const snappedX = Math.round(dx / props.cellSize) * props.cellSize;
            const snappedY = Math.round(dy / props.cellSize) * props.cellSize;

            table.position_x = snappedX;
            table.position_y = snappedY;

            }
        };

        selected = !selected;

        if (selected) {
            console.log('table selected')
            document.addEventListener("mousemove", moveHandler);
        } else {
            console.log('table not selected')
            document.removeEventListener("mousemove", moveHandler);
        }
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
</style>