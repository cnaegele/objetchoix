<template>
  <v-card>
    <v-tabs v-model="tabchoisi">
      <v-tab value="batiment" v-if="props.batiment === 'oui'">Bâtiments</v-tab>
      <v-tab value="parcelle" v-if="props.parcelle === 'oui'">Parcelles</v-tab>        
      <v-tab value="batpar" v-if="props.batpar === 'oui'">Bâtiments et Parcelles par adresse</v-tab>        
      <v-tab value="rue" v-if="props.rue === 'oui'">Rues</v-tab>        
    </v-tabs>

    <v-card-text>
      <v-tabs-window v-model="tabchoisi">

        <v-tabs-window-item value="batiment" v-if="props.batiment === 'oui'">
            <BatimentChoix typeCritere="nom" :ssServer="ssServer"></BatimentChoix>
        </v-tabs-window-item>

        <v-tabs-window-item value="parcelle" v-if="props.parcelle === 'oui'">
            <ParcelleChoix :ssServer="ssServer"></ParcelleChoix>
        </v-tabs-window-item>

        <v-tabs-window-item value="batpar" v-if="props.batpar === 'oui'">
            <BatimentParcelleChoix :ssServer="ssServer"></BatimentParcelleChoix>
        </v-tabs-window-item>

        <v-tabs-window-item value="rue" v-if="props.rue === 'oui'">
            <RueChoix :ssServer="ssServer"></RueChoix>
        </v-tabs-window-item>

    </v-tabs-window>   
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Props {
  modeChoix?: string
  batiment?: string
  parcelle?: string
  batpar?: string
  rue?: string
  adresse?: string
  autre?: string
  nombreMaximumRetour?: number
  ssServer?: string
  ssPage?: string
}

const props = withDefaults(defineProps<Props>(), {
  modeChoix: 'unique',
  batiment: "oui",
  parcelle: "oui",
  batpar: "oui",
  rue: "oui",
  adresse: "oui",
  autre: "oui",
  nombreMaximumRetour: 100,
  ssServer: '',
  ssPage: '/goeland/acteur/ajax/acteur_liste.php'
})

const tabchoisi = ref<string | null>(null)
const ssServer = ref<string>(props.ssServer)

</script>

<style scoped>
.v-tab {
  text-transform: none !important;
}
</style>