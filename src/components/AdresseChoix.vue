<template>
  <div class="d-flex align-center">
    <span class="me-4">recherche adresse selon</span>
    <v-radio-group v-model="typeCritere" inline density="compact" hide-details>
      <v-radio label="début nom principal rue, numéro" :value="'nom'" class="me-4"></v-radio>
      <v-radio label="adresse complète, numéro" :value="'nomcomplet'"></v-radio>
    </v-radio-group>
  </div>
  <div class="d-flex align-center">
    <span class="me-4">choix adresse</span>
    <v-radio-group v-model="modeChoix" inline density="compact" hide-details>
      <v-radio label="unique" :value="'unique'" class="me-4"></v-radio>
      <v-radio label="multiple" :value="'multiple'"></v-radio>
    </v-radio-group>
  </div>
  <v-text-field dense clearable v-model="txtCritere" ref="inpTxtCritere" label="adresse" class="flex-0-0"
    style="width: 400px; min-width: 400px;" @input="onInputCritere"></v-text-field>
  <v-list max-height="400">
    <v-list-subheader>{{ libelleListe }}</v-list-subheader>
    <v-list-item v-for="adresse in adressesListeSelect" :key="adresse.idadresse" :value="adresse.idadresse"
      :title="`${adresse.rue} ${adresse.numero}`" @click="choixAdresse(adresse)">
      <template v-slot:append>
        <v-btn color="grey-lighten-1" icon="mdi-map-outline" variant="text" @mouseenter="infoMouseEnter()"
          @mouseleave="infoMouseLeave()" @click="openGC(adresse.coordeo, adresse.coordsn)"></v-btn>

      </template>



    </v-list-item>
  </v-list>
  <v-list max-height="400" v-if="modeChoix === 'multiple'">
    <v-list-subheader>
      {{ libelleListeChoisi }}&nbsp;&nbsp;&nbsp;&nbsp;
      <v-btn rounded="lg" @click="choixTermine()">Choix terminé</v-btn>
    </v-list-subheader>
    <v-list-item v-for="adresse in adressesChoisi" :key="adresse.idadresse" :value="adresse.idadresse"
      :title="`${adresse.rue} ${adresse.numero}`">
      <template v-slot:append>
        <v-btn color="grey-lighten-1" icon="mdi-delete" variant="text"
          @click="supprimeChoix(adresse.idadresse)"></v-btn>
      </template>
    </v-list-item>
  </v-list>

</template>

<script setup lang="ts">
import type { Adresse, ApiResponseAL } from '@/axioscalls.js'
import { ref, watch } from 'vue'
import { getAdressesListe } from '@/axioscalls.js'

interface Props {
  modeChoix?: string
  typeCritere?: string
  nombreMaximumRetour?: number
  ssServer?: string
  ssPage?: string
}

interface CritereRecherche {
  critererue: string
  criterenumero: string
  criterenumeroext: string
  crtype: string
  nombremaximumretour: number
}

const props = withDefaults(defineProps<Props>(), {
  modeChoix: 'unique',
  typeCritere: 'nom',
  nombreMaximumRetour: 100,
  ssServer: '',
  ssPage: '/goeland/adresse/axios/adresse_liste.php'
})

const modeChoix = ref<string>(props.modeChoix)
const nombreMaximumRetour = ref<number>(props.nombreMaximumRetour)
const ssServer = ref<string>(props.ssServer)
const ssPage = ref<string>(props.ssPage)

const typeCritere = ref<string>('nom')
const txtCritere = ref<string>('')
const inpTxtCritere = ref<any>(null)

const messageErreur = ref<string>('')
const libelleListe = ref<string>('selection adresses (0)')
const adressesListeSelect = ref<Adresse[]>([])
let demandeOpenGC: boolean = false

const adressesChoisi = ref<Adresse[]>([])
const libelleListeChoisi = ref<string>(`adresses choisies (${adressesChoisi.value.length})`)


//pour démarrer la recherche seulement si la frappe au clavier a cessé depuis 0.7 secondes
let typingTimer: number | null = null
const typingInterval: number = 700
const onInputCritere = (): void => {
  // console.log('oninput')
  if (typingTimer) {
    clearTimeout(typingTimer)
  }

  typingTimer = setTimeout(() => {
    prepareRecherche()
  }, typingInterval)

  if (inpTxtCritere.value?.$el?.querySelector('input')) {
    inpTxtCritere.value.$el.querySelector('input').focus()
  }
}

watch(() => txtCritere.value, (newValue: string | null): void => {
  if (newValue === null || newValue === '') {
    libelleListe.value = `Selection adresses (0)`
    adressesListeSelect.value = []
  }
})

const prepareRecherche = (): void => {
  const critere: string = txtCritere.value.trim()
  const typeCr: string = typeCritere.value
  let critereRue: string = critere
  let critereNumero: string = ''
  let critereNumExt: string = ''
  if ((typeCr == 'nom' && critere.length >= 2) || (typeCr == 'nomcomplet' && critere.length >= 4)) {
    //recherche d'un éventuel numéro, de 1 à 4 chiffres
    const numero: string | null = critere.match(/(?:\D|^)([1-9]|[1-9]\d{1,3})(?=\D|$)/)?.[1] ?? null
    if (numero !== null) {
      const posi: number = critere.indexOf(numero)
      const avantNumero: string = critere.substring(0, posi).trim()
      const apresNumero: string = critere.substring(posi + numero.length).trim()
      if (apresNumero.length <= 4) {
        //il faut supporter le gbis de lausanne mais si on a plus, on a quelque chose du genre "24 janvier" "chemin des 3 fontaines" 
        critereRue = avantNumero
        critereNumero = numero
        critereNumExt = apresNumero
      }
    }
    recherche(typeCr, critereRue, critereNumero, critereNumExt, nombreMaximumRetour.value)
  }
}

const recherche = async (crType: string, critereRue: string, critereNumero: string, critereNumExt: string, nombreMaximumRetour: number): Promise<void> => {
  const oCritere: CritereRecherche = {
    critererue: critereRue,
    criterenumero: critereNumero,
    criterenumeroext: critereNumExt,
    crtype: crType,
    nombremaximumretour: nombreMaximumRetour
  }
  const response: ApiResponseAL = await getAdressesListe(ssServer.value, ssPage.value, JSON.stringify(oCritere))
  const returnListe: Adresse[] = response.success && response.data ? response.data : []
  //console.log(returnListe)
  if (returnListe.length < nombreMaximumRetour) {
    libelleListe.value = `Selection adresses (${returnListe.length})`
  } else {
    libelleListe.value = `Selection adresses (${returnListe.length}). Attention, plus de ${nombreMaximumRetour} adresses correspondent aux critères`
  }
  adressesListeSelect.value = returnListe
}

const emit = defineEmits<{
  (e: 'choixAdresse', id: number, choix: string): void
}>()

const infoMouseEnter = (): void => {
  demandeOpenGC = true
}

const infoMouseLeave = (): void => {
  demandeOpenGC = false
}

const choixAdresse = (adresse: Adresse): void => {
  if (!demandeOpenGC) {
    if (modeChoix.value === 'unique') {
      console.log(adresse)
      emit('choixAdresse', adresse.idadresse, JSON.stringify(adresse))
    } else {
      if (adressesChoisi.value.some(adr => adr.idadresse === adresse.idadresse) === false) {
        adressesChoisi.value.push(adresse)
        libelleListeChoisi.value = `adresses choisies (${adressesChoisi.value.length})`
      }
    }
  }
}

const openGC = (x: number, y: number) => {
  window.open(`https://carto.lausanne.ch/?map_x=${x}&map_y=${y}y&map_zoom=7`, "gclausanne")
}

const supprimeChoix = (idAdresse: number) => {
  const index = adressesChoisi.value.findIndex(adresse => adresse.idadresse === idAdresse);
  if (index > -1) {
    adressesChoisi.value.splice(index, 1);
  }
}

const choixTermine = () => {
  emit('choixAdresse', 0, JSON.stringify(adressesChoisi.value))
  adressesChoisi.value = []
  libelleListeChoisi.value = `adresses choisies (${adressesChoisi.value.length})`
}

</script>

<style scoped></style>