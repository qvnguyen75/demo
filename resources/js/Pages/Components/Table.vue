<template>
    <div
    v-for="(table, index) in tables"
    :key="index"
    :id="table.id"
    class="table"
    :class="{start: table.start, end: table.end}"
    :style="tableStyle(table)"
    @click.self="startDrag(table, $event)"
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
        tables: Array,
        nodes: Array,
        zoomLevel:Number,
        cellSize: Number
    })

    let correspondingTableNode = ref(Object);

    let tableSelected = false;

    const makeMoveHandler = (table, initialX, initialY) => {
        return function (event) {
            if (tableSelected) {
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
        }
            
    };

    const startDrag = (table, event) => {
        emit('onTableClick', isStartTableSet(), isEndTableSet());

        let tableHtml = document.getElementById(table.id);
        const initialX = event.clientX - table.position_x;
        const initialY = event.clientY - table.position_y;

        let moveHandler = makeMoveHandler(table, initialX, initialY);

        if (tableSelected) {
            tableHtml.removeEventListener("mousemove", moveHandler);
            tableSelected = false;

            // save corresponding table node
            table.node_id = correspondingTableNode.id;
            router.put('/', table);
            return;
        }

        tableSelected = true;

        if (isStartTableSet() && isEndTableSet()) {
            tableHtml.addEventListener("mousemove", moveHandler);
        } else if(isStartTableSet() && !isEndTableSet()) {
            tableHtml.addEventListener("mousemove", moveHandler);
            table.end = true;
        } else {
            tableHtml.addEventListener("mousemove", moveHandler);
            table.start = true;
        }
    }

    const isStartTableSet = () => {
        return props.tables.find(table => table.start === true)
    }

    const isEndTableSet = () => {
        return props.tables.find(table => table.end === true)
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
  width: 150px;
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