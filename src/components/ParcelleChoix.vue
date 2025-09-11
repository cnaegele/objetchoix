<template>
  <div class="d-flex align-items-baseline">
    <span class="me-2 mt-3">critère selon :</span>
    <v-radio-group v-model="typeCritere" inline>
      <v-radio label="numéro et commune" value="numero" class="me-4"></v-radio>
      <v-radio label="n° ECA et commune" value="eca" class="me-4"></v-radio>
      <v-radio label="egrid" value="egrid"></v-radio>
    </v-radio-group>
  </div>
  <div class="d-flex align-items-baseline">
    <v-text-field dense clearable v-model="txtCritere" ref="inpTxtCritere" :label="libelleInpCritere" class="flex-0-0"
      style="width: 400px; min-width: 400px;" @input="onInputCritere"></v-text-field>
    &nbsp;&nbsp;
    <v-autocomplete v-if="typeCritere !== 'egrid'" v-model="idOFSCommune" label="Commune" :items="communesListeSelect"
      :custom-filter="communesCustomFilter" item-title="nom" item-value="id" class="flex-0-0"
      style="width: 400px; min-width: 400px;" no-virtual></v-autocomplete>
  </div>

  <v-list max-height="400">
    <v-list-subheader>{{ libelleListe }}</v-list-subheader>
    <v-list-item v-for="parcelle in parcellesListeSelect" :key="parcelle.id" :value="parcelle.id" :title="parcelle.nom"
      @click="choixParcelle(parcelle)">
      <template v-slot:append>
        <v-btn color="grey-lighten-1" icon="mdi-information" variant="text" @mouseenter="infoMouseEnter()"
          @mouseleave="infoMouseLeave()" @click="openFicheObjet(parcelle.id)"></v-btn>
      </template>
    </v-list-item>
  </v-list>

</template>

<script setup lang="ts">
import type { Objet, ApiResponseOL } from '@/axioscalls.js'
import type { FilterFunction } from 'vuetify'
import { ref, watch, onMounted } from 'vue'
import { getParcellesListe, getCommunesParcelleListe } from '@/axioscalls.js'

interface Props {
  typeCritere?: string
  nombreMaximumRetour?: number
  ssServer?: string
  ssPage?: string
  ssPageCommune?: string
}

interface CritereRecherche {
  critere: string
  idcommune: number
  crtype: string
  nombremaximumretour: number
}

interface AutocompleteItem {
  title: string
  value: any
  raw: any
}

const props = withDefaults(defineProps<Props>(), {
  typeCritere: 'numero',
  nombreMaximumRetour: 100,
  ssServer: '',
  ssPage: '/goeland/parcelle/axios/parcelle_liste.php',
  ssPageCommune: '/goeland/parcelle/axios/communes_liste.php'
})

const typeCritereInitial = props.typeCritere
const typeCritere = ref<string>(typeCritereInitial)
const nombreMaximumRetour = ref<number>(props.nombreMaximumRetour)
const txtCritere = ref<string>('')
const libelleInpCritere = ref<string>('')

switch (typeCritereInitial) {
  case 'numero':
    libelleInpCritere.value = 'numéro parcelle'
    break
  case 'eca':
    libelleInpCritere.value = 'n° ECA'
    break
  case 'egid':
    libelleInpCritere.value = 'egrid'
    break
  default:
    // Gestion du cas par défaut pour la sécurité
    libelleInpCritere.value = '???'
    break
}
const ssServer = ref<string>(props.ssServer)
const ssPage = ref<string>(props.ssPage)
const ssPageCommune = ref<string>(props.ssPageCommune)

const messageErreur = ref<string>('')
const libelleListe = ref<string>('choix parcelles (0)')
const idOFSCommune = ref<number>(5586)
const communesListeSelect = ref<Objet[]>([])
const parcellesListeSelect = ref<Objet[]>([])
let demandeInformation: boolean = false

const inpTxtCritere = ref<any>(null)

onMounted(() => {
  //Liste des communes avec des parcelles
  listeCommune()
})

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

const listeCommune = async (): Promise<void> => {
  const response: ApiResponseOL = await getCommunesParcelleListe(ssServer.value, ssPageCommune.value,)
  const returnListe: Objet[] = response.success && response.data ? response.data : []
  communesListeSelect.value = returnListe
}

const prepareRecherche = (): void => {
  const critere: string = txtCritere.value.trim()
  const typeCr: string = typeCritere.value
  if (critere !== '') {
    switch (typeCr) {
      case 'numero':
        if (critere.length !== 0) {
          recherche('numero', critere, idOFSCommune.value, nombreMaximumRetour.value)
        }
        break
      case 'eca':
        recherche('eca', critere, idOFSCommune.value, nombreMaximumRetour.value)
        break
      case 'egrid':
        recherche('egrid', critere, 0, nombreMaximumRetour.value)
        break
    }
  }
}

const recherche = async (crType: string, critere: string, idCommune: number, nombreMaximumRetour: number): Promise<void> => {
  const oCritere: CritereRecherche = {
    crtype: crType,
    critere: critere,
    idcommune: idCommune,
    nombremaximumretour: nombreMaximumRetour
  }
  const response: ApiResponseOL = await getParcellesListe(ssServer.value, ssPage.value, JSON.stringify(oCritere))
  const returnListe: Objet[] = response.success && response.data ? response.data : []
  if (returnListe.length < nombreMaximumRetour) {
    libelleListe.value = `Choix parcelles (${returnListe.length})`
  } else {
    libelleListe.value = `Choix parcelles (${returnListe.length}). Attention, plus de ${nombreMaximumRetour} bâtiments correspondent aux critères`
  }
  parcellesListeSelect.value = returnListe
}

const communesCustomFilter: FilterFunction = (
  itemTitle: string,
  queryText: string,
  item?: any
): boolean => {
  if (!queryText || !item) return true

  const removeAccents = (str: string): string =>
    str.normalize('NFD').replace(/[\u0300-\u036f]/g, '')

  const textLowerCase = itemTitle.toLowerCase()
  const searchTextLowerCase = queryText.toLowerCase()
  const textUnAccent = removeAccents(itemTitle)
  const searchTextUnAccent = removeAccents(queryText.toLowerCase())

  return textLowerCase.includes(searchTextLowerCase) ||
    textUnAccent.includes(searchTextUnAccent)
}

watch(() => idOFSCommune.value, (newValue: number): void => {
  prepareRecherche()
})

watch(() => typeCritere.value, (newValue: string): void => {
  switch (newValue) {
    case 'numero':
      libelleInpCritere.value = 'numéro parcelle'
      break
    case 'eca':
      libelleInpCritere.value = 'n° ECA'
      break
    case 'egrid':
      libelleInpCritere.value = 'egrid'
      break
    default:
      // Gestion du cas par défaut pour la sécurité
      libelleInpCritere.value = '???'
      break
  }
  prepareRecherche()
})

watch(() => txtCritere.value, (newValue: string | null): void => {
  if (newValue === null || newValue === '') {
    libelleListe.value = `Selection adresses (0)`
    parcellesListeSelect.value = []
  }
})

const emit = defineEmits<{
  (e: 'choixParcelle', id: number, choix: string): void
}>()

const choixParcelle = (parcelle: Objet): void => {
  if (!demandeInformation) {
    emit('choixParcelle', parcelle.id, JSON.stringify(parcelle))
  }
}

const infoMouseEnter = (): void => {
  demandeInformation = true
}

const infoMouseLeave = (): void => {
  demandeInformation = false
}

const openFicheObjet = (idobjet: number): void => {
  window.open(`https://golux.lausanne.ch/goeland/objet/getobjetinfo.php?idObjet=${idobjet}`, "goobjetinfo")
}
</script>

<style scoped></style>