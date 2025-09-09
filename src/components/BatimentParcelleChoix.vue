<template>
    <v-radio-group
        label="retour de"
        v-model="retourType"
        inline
        density="compact">
        <v-radio label="parcelles et bâtiments" :value="'parbat'" class="me-4"></v-radio>
        <v-radio label="bâtiments" :value="'bat'" class="me-4"></v-radio>
        <v-radio label="parcelles" :value="'par'"></v-radio>
    </v-radio-group>
    <AdresseChoix></AdresseChoix>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdresseChoix from './AdresseChoix.vue'

const retourType = ref<string>('parbat')


</script>

<style scoped>

</style>