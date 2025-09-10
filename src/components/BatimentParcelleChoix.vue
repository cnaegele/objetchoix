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
    <AdresseChoix :ssServer="ssServer"></AdresseChoix>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdresseChoix from './AdresseChoix.vue'

interface Props {
  nombreMaximumRetour?: number
  ssServer?: string
  ssPageBatiment?: string
  ssPageParcelle?: string
}
const props = withDefaults(defineProps<Props>(), {
  nombreMaximumRetour: 100,
  ssServer: '',
  ssPageBatiment: '/goeland/batiment/axios/batiment_liste_paradresse.php',
  ssPageParcelle: '/goeland/parcelle/axios/parcelle_liste_paradresse.php'
})

const ssServer = ref<string>(props.ssServer)
const retourType = ref<string>('parbat')


</script>

<style scoped>

</style>