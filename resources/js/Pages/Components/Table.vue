<template>
    <div
    v-for="(table, index) in tables"
    :key="index"
    class="table"
    :style="tableStyle(table)"
    draggable="true"
    @mousedown="startDrag(index, $event)"
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

    const startDrag = (index, event) => {
        const table = props.tables[index];

        const initialX = event.clientX - table.x;
        const initialY = event.clientY - table.y;
        
        const moveHandler = (event) => {
            // Calculate the distance moved
            const dx = event.clientX - initialX;
            const dy = event.clientY - initialY;

            // const gridSize = 50;
            const snappedX = Math.round(dx / props.cellSize) * props.cellSize;
            const snappedY = Math.round(dy / props.cellSize) * props.cellSize;

            table.x = snappedX;
            table.y = snappedY;

        };

        const upHandler = () => {
            document.removeEventListener("mousemove", moveHandler);
            document.removeEventListener("mouseup", upHandler);
        };

        document.addEventListener("mousemove", moveHandler);
        document.addEventListener("mouseup", upHandler);
    }

    const tableStyle = computed(() => {
        return (table) => ({
            left: table.x + 'px',
            top: table.y + 'px',
            transform: `scale(${props.zoomLevel})`
        });
    });
</script>

<style scoped>
.table {
  width: 300px;
  height: 400px;
  border: 1px solid #000;
  background-color: #f9f9f9;
  position: absolute;
  cursor: move;
}
</style>