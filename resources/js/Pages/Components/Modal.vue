<template>
<div class="modal">
    <div class="modal-content">
        <span class="close" @click="closeModal">&times;</span>
        <form @submit.prevent="saveTable">
        <label for="tableName">Table Name:</label>
        <input type="text" id="tableName" v-model="tableName" required>
        <label for="property">Property:</label>
        <input type="text" id="property" v-model="property" required>
        <button type="submit">Save</button>
        </form>
    </div>
</div>
</template>

<script setup>
    import {  defineProps, ref } from 'vue';

    const emit      = defineEmits(['tableSaved', 'modalClosed'])
    const tableName = ref('');
    const property  = ref('');

    const props = defineProps({
      showModal: {
        type: Boolean
      }
    })

    const saveTable = () => {
        const table = {
            tableName: tableName.value,
            property: property.value
        }

        emit('tableSaved', table);

        tableName.value = '';
        property.value  = '';
    }

    const closeModal = () => {
      emit('modalClosed', true)
    }
</script>

<style>
.modal {
  display: block;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4);
}
.modal-content {
  background-color: #fefefe;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}
.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>