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
          <BatimentChoix typeCritere="nom" :ssServer="ssServer" @choixBatiment="receptionObjet"></BatimentChoix>
        </v-tabs-window-item>

        <v-tabs-window-item value="parcelle" v-if="props.parcelle === 'oui'">
          <ParcelleChoix :ssServer="ssServer" @choixParcelle="receptionObjet"></ParcelleChoix>
        </v-tabs-window-item>

        <v-tabs-window-item value="batpar" v-if="props.batpar === 'oui'">
          <BatimentParcelleChoix :ssServer="ssServer" @choixBatPar="receptionObjet"></BatimentParcelleChoix>
        </v-tabs-window-item>

        <v-tabs-window-item value="rue" v-if="props.rue === 'oui'">
          <RueChoix :ssServer="ssServer" @choixRue="receptionObjet"></RueChoix>
        </v-tabs-window-item>
      </v-tabs-window>
    </v-card-text>
  </v-card>
  <v-list max-height="400" v-if="modeChoix === 'multiple'">
    <v-list-subheader>
      {{ libelleListe }}&nbsp;&nbsp;&nbsp;&nbsp;
      <v-btn rounded="lg" @click="choixTermine()">Choix terminé</v-btn>
    </v-list-subheader>
    <v-list-item v-for="objet in objetsChoisi" :key="objet.id" :value="objet.id" :title="objet.nom">
      <template v-slot:append>
        <v-btn color="grey-lighten-1" icon="mdi-information" variant="text" @click="openFicheObjet(objet.id)"></v-btn>
        <v-btn color="grey-lighten-1" icon="mdi-delete" variant="text" @click="supprimeChoix(objet.id)"></v-btn>

      </template>
    </v-list-item>
  </v-list>

</template>

<script setup lang="ts">
import type { Objet } from '@/axioscalls.js'
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
})

const tabchoisi = ref<string | null>(null)
const modeChoix = ref<string>(props.modeChoix)
const ssServer = ref<string>(props.ssServer)
const objetsChoisi = ref<Objet[]>([])
const libelleListe = ref<string>(`objets choisis (${objetsChoisi.value.length})`)

console.log(modeChoix.value)

const emit = defineEmits<{
  (e: 'choixObjet', id: number, choix: string): void
}>()

const receptionObjet = (id: number, jsonData: string) => {
  if (modeChoix.value == 'unique') {
    emit('choixObjet', id, jsonData)
  } else {
    let aObjet: Objet[] = []
    const retObjet: Objet | Objet[] = JSON.parse(jsonData)
    if (!Array.isArray(retObjet)) {
      aObjet.push(retObjet)
    } else {
      aObjet = retObjet
    }
    for (const objet of aObjet) {
      if (objetsChoisi.value.some(obj => obj.id === objet.id) === false) {
        objetsChoisi.value.push(objet)
        libelleListe.value = `objets choisis (${objetsChoisi.value.length})`
      }
    }
  }
}

const supprimeChoix = (id: number) => {
  const index = objetsChoisi.value.findIndex(objet => objet.id === id);
  if (index > -1) {
    objetsChoisi.value.splice(index, 1);
  }
}

const choixTermine = () => {
  emit('choixObjet', 0, JSON.stringify(objetsChoisi.value))
  objetsChoisi.value = []
  libelleListe.value = `objets choisis (${objetsChoisi.value.length})`
}

const openFicheObjet = (idobjet: number): void => {
  window.open(`https://golux.lausanne.ch/goeland/objet/getobjetinfo.php?idObjet=${idobjet}`, "goobjetinfo")
}  
</script>

<style scoped>
.v-tab {
  text-transform: none !important;
}
</style>