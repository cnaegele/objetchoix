<template>
    <div class="d-flex align-items-baseline">  
        <v-autocomplete
            v-model="rueChoisie"
            :label="labelChoixRue"
            :items="ruesListeSelect"
            :custom-filter="ruescommunesCustomFilter"
            item-title="nom"
            item-value="id"
            return-object
            class="flex-0-0"
            style="width: 400px; min-width: 400px;"
            no-virtual
            clearable
        ></v-autocomplete>        
        &nbsp;&nbsp;
        <v-autocomplete
            v-model="communeChoisie"
            label="Commune"
            :items="communesListeSelect"
            :custom-filter="ruescommunesCustomFilter"
            item-title="nom"
            item-value="id"
            return-object
            class="flex-0-0"
            style="width: 400px; min-width: 400px;"
            no-virtual
            clearable
        ></v-autocomplete>
    </div>
</template>

<script setup lang="ts">
import type { Objet, ApiResponseOL } from '@/axioscalls.js'
import type { FilterFunction } from 'vuetify'
import { ref, watch, onMounted } from 'vue'
import { getRuesListe, getCommunesRueListe} from '@/axioscalls.js'

interface Props {
  nombreMaximumRetour?: number
  ssServer?: string
  ssPage?: string
  ssPageCommune?: string
}

interface CritereRecherche {
  idcommune: number
}

interface AutocompleteItem {
  title: string
  value: any
  raw: any
}

const props = withDefaults(defineProps<Props>(), {
  ssServer: '',
  ssPage: '/goeland/rue/axios/rues_liste.php',
  ssPageCommune: '/goeland/rue/axios/communes_liste.php'
})

const ssServer = ref<string>(props.ssServer)
const ssPage = ref<string>(props.ssPage)
const ssPageCommune = ref<string>(props.ssPageCommune)

const labelChoixRue = ref<string>('choix rue Lausanne')
const messageErreur = ref<string>('')
const idGoCommune = ref<number>(632)
const communesListeSelect = ref<Objet[]>([])
const ruesListeSelect = ref<Objet[]>([])
const communeChoisie = ref<Objet | null>({
  id: 632,
  nom: 'Lausanne',
  idstatut: ''
})
const rueChoisie = ref<Objet | null>(null)
let demandeInformation: boolean = false

const inpTxtCritere = ref<any>(null)

onMounted(() => {
  //Liste des communes avec des parcelles
  listeCommunes()
  listeRues(idGoCommune.value)
})

const listeCommunes = async (): Promise<void> => {
  console.log(idGoCommune.value)
  const response: ApiResponseOL = await getCommunesRueListe(ssServer.value, ssPageCommune.value,)
  const returnListe: Objet[] = response.success && response.data ? response.data : []
  communesListeSelect.value = returnListe
}

const listeRues = async (idCommune: number): Promise<void> => {
    const oCritere: CritereRecherche = {
        idcommune: idCommune
    }
  const response: ApiResponseOL = await getRuesListe(ssServer.value, ssPage.value, JSON.stringify(oCritere))
  const returnListe: Objet[] = response.success && response.data ? response.data : []
  ruesListeSelect.value = returnListe
}

watch(() => communeChoisie.value, (): void => {
    if (communeChoisie.value !== null) {
        labelChoixRue.value = `choix rue ${communeChoisie.value.nom}`
        listeRues(communeChoisie.value.id)
    }
})

watch(() => rueChoisie.value, (): void => {
    if (rueChoisie.value !== null) {
        choixRue(rueChoisie.value)
    }
})

const emit = defineEmits<{
  (e: 'choixRue', id: number, choix: string): void
}>()

const choixRue = (rue: Objet): void => {
    emit('choixRue', rue.id, JSON.stringify(rue))
}

const ruescommunesCustomFilter: FilterFunction = (
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




</script>

<style scoped>

</style>