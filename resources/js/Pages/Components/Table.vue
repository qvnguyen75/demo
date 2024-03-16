<template>
    <div
    v-for="(table, index) in tables"
    :key="index"
    class="table"
    :class="{selected: table.selected}"
    :style="tableStyle(table)"
    draggable="true"
    @click="startDrag(table, $event)"
    >
        {{ table.name }}
        <br>
        {{ table.property }}
    </div>
</template>

<script setup>
    import { defineProps, computed } from 'vue';
    import { router } from '@inertiajs/vue3';

    const props = defineProps({
        tables: {
            type: Array
        },
        zoomLevel: {
            type: Number
        },
        cellSize: Number
    })

    const startDrag = (table, event) => {
        const initialX = event.clientX - table.position_x;
        const initialY = event.clientY - table.position_y;

        const moveHandler = (event) => {
            if (table.selected) {
                // Calculate the distance moved
                const dx = event.clientX - initialX;
                const dy = event.clientY - initialY;

                const snappedX = Math.round(dx / props.cellSize) * props.cellSize;
                const snappedY = Math.round(dy / props.cellSize) * props.cellSize;

                table.position_x = snappedX;
                table.position_y = snappedY;
            }
        };

        table.selected= !table.selected;

        if (table.selected) {
            console.log('table selected');
            document.addEventListener("mousemove", moveHandler);
        } else {
            console.log('table not selected')
            document.removeEventListener("mousemove", moveHandler);
            // console.log(table);
            router.put('/', table);
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

.selected {
    background-color: green;
}
</style>