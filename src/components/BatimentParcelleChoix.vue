<template>
  <div v-if="messageErreur != ''" id="divErreur">{{ messageErreur }}</div>
  <div class="d-flex align-center">
    <span class="me-4">retour de</span>
    <v-radio-group v-model="retourType" inline density="compact" hide-details class="flex-grow-0 me-4">
      <v-radio label="parcelles et bâtiments" :value="'parbat'" class="me-4"></v-radio>
      <v-radio label="bâtiments" :value="'bat'" class="me-4"></v-radio>
      <v-radio label="parcelles" :value="'par'"></v-radio>
    </v-radio-group>
    <v-checkbox v-if="retourType === 'parbat' || retourType === 'par'" label="avec parcelles PPE" v-model="bPPE" density="compact" hide-details></v-checkbox>
  </div>
  <AdresseChoix :modeChoix="modeChoixAdresse" :ssServer="ssServer" @choixAdresse="receptionAdresse"></AdresseChoix>
</template>

<script setup lang="ts">
import type { Adresse, Objet, ApiResponseOL } from '@/axioscalls.js'
import { ref } from 'vue'
import { getBatimentsListeParAdresse, getParcellesListeParAdresse } from '@/axioscalls.js'
import AdresseChoix from './AdresseChoix.vue'

interface Props {
  nombreMaximumRetour?: number
  ssServer?: string
  ssPageBatiment?: string
  ssPageParcelle?: string
}
interface CritereRecherche {
  idadresse: number
  bppe: string
  typeretoursp: string
}
const props = withDefaults(defineProps<Props>(), {
  nombreMaximumRetour: 100,
  ssServer: '',
  ssPageBatiment: '/goeland/batiment/axios/batiment_liste_paradresse.php',
  ssPageParcelle: '/goeland/parcelle/axios/parcelle_liste_paradresse.php'
})

const messageErreur = ref<string>('')
const modeChoixAdresse = ref<string>('unique')
const ssServer = ref<string>(props.ssServer)
const ssPageBatiment = ref<string>(props.ssPageBatiment)
const ssPageParcelle = ref<string>(props.ssPageParcelle)
const retourType = ref<string>('parbat')
const bPPE = ref<boolean>(false)
let batparListe: Objet[] = []

const receptionAdresse = async (id: number, jsonData: string) => {
  let adresses: Adresse[] = []
  const retAdresse: Adresse | Adresse[] = JSON.parse(jsonData)
  let nbPPE: string = '0'
  if (bPPE.value) {
    nbPPE = '1'  
  }
  if (!Array.isArray(retAdresse)) {
    adresses.push(retAdresse)
  } else {
    adresses = retAdresse
  }
  for (const adresse of adresses) {
    const oCritere: CritereRecherche = {
      "idadresse": adresse.idadresse,
      "bppe": nbPPE,
      "typeretoursp": 'objet'
    }

    if (retourType.value === 'parbat' || retourType.value === 'bat') {
      //Recherche batiment par adresse
      await rechercheBatiment(JSON.stringify(oCritere))
    }
    if (retourType.value === 'parbat' || retourType.value === 'par') {
      //Recherche batiment par adresse
      await rechercheParcelle(JSON.stringify(oCritere))
    }
  }
  if (batparListe.length === 1) {
    emit('choixBatPar', batparListe[0].id, JSON.stringify(batparListe))
  } else {
    emit('choixBatPar', 0, JSON.stringify(batparListe))
  }
  batparListe = []

}

const emit = defineEmits<{
  (e: 'choixBatPar', id: number, choix: string): void
}>()

const rechercheBatiment = async (jsoncritere: string): Promise<void> => {
  const response: ApiResponseOL = await getBatimentsListeParAdresse(ssServer.value, ssPageBatiment.value, jsoncritere)
  if (response.success === false) {
    messageErreur.value = response.message
  } else {
    messageErreur.value = ''    
  }
  const returnListe: Objet[] = response.success && response.data ? response.data : []
  //console.log(returnListe)
  for (const objet of returnListe) {
    batparListe.push(objet)
  }
}

const rechercheParcelle = async (jsoncritere: string): Promise<void> => {
  const response: ApiResponseOL = await getParcellesListeParAdresse(ssServer.value, ssPageParcelle.value, jsoncritere)
  if (response.success === false) {
    messageErreur.value = response.message
  } else {
    messageErreur.value = ''    
  }
  const returnListe: Objet[] = response.success && response.data ? response.data : []
  //console.log(returnListe)
  for (const objet of returnListe) {
    batparListe.push(objet)
  }
}
</script>

<style scoped></style>