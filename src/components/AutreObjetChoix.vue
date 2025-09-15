<template>
    <div v-if="messageErreur != ''" id="divErreur">{{ messageErreur }}</div>
    <div class="d-flex align-items-baseline">
        <div style="width: 400px; flex-shrink: 0;" class="me-4">
            <v-text-field dense clearable v-model="txtCritere" ref="inpTxtCritere" label="nom ou id goéland"
                class="mb-4" @input="onInputCritere"></v-text-field>
        </div>
        <v-select v-if="!bTOFavori" v-model="idTypeObjet" :items="typesObjetsTous" item-title="nom" item-value="id"
            label="type d'objet" class="mb-4 me-4 flex-shrink-1 flex-grow-0" style="min-width: 300px;" dense></v-select>
        <v-select v-if="bTOFavori" v-model="idTypeObjet" :items="typesObjetsFavoris" item-title="nom" item-value="id"
            label="type d'objet, liste des favoris" class="mb-4 me-4 flex-shrink-1 flex-grow-0"
            style="min-width: 300px;" dense></v-select>
        <v-radio-group v-if="bFavoriExist" v-model="codeFavoriTous" dense inline class="flex-shrink-0">
            <v-radio label="types d'objet favoris" value="favori" class="mb-2 me-4"></v-radio>
            <v-radio label="tous les types d'objet" value="tous" class="mb-2"></v-radio>
        </v-radio-group>
    </div>

  <v-list max-height="400">
    <v-list-subheader>{{ libelleListe }}</v-list-subheader>
    <v-list-item v-for="objet in objetsListeSelect" :key="objet.id" :value="objet.id" :title="objet.nom"
      @click="choixAutreObjet(objet)">
      <template v-slot:append>
        <v-btn color="grey-lighten-1" icon="mdi-information" variant="text" @mouseenter="infoMouseEnter()"
          @mouseleave="infoMouseLeave()" @click="openFicheObjet(objet.id)"></v-btn>
      </template>
    </v-list-item>
  </v-list>
</template>

<script setup lang="ts">
import type { ComputedRef } from 'vue'
import type { TypeObjet, Objet, ApiResponseTOL, ApiResponseOL } from '@/axioscalls.js'
import { ref, watch, onMounted, computed } from 'vue'
import { getTypeObjetListe, getObjetsListe } from '@/axioscalls.js'

interface Props {
    nombreMaximumRetour?: number
    ssServer?: string
    ssPage?: string
    ssPageType?: string
}

interface CritereRecherche {
    critere: string
    idtype: number
    nombremaximumretour: number
}

const props = withDefaults(defineProps<Props>(), {
    nombreMaximumRetour: 100,
    ssServer: '',
    ssPage: '/goeland/objet/axios/objet_liste.php',
    ssPageType: '/goeland/objet/axios/typeobjet_liste.php'
})

const codeFavoriTous = ref<string>('favori')
const nombreMaximumRetour = ref<number>(props.nombreMaximumRetour)
const txtCritere = ref<string>('')
const idTypeObjet = ref<number>(0)
const ssServer = ref<string>(props.ssServer)
const ssPage = ref<string>(props.ssPage)
const ssPageType = ref<string>(props.ssPageType)
const messageErreur = ref<string>('')
const libelleListe = ref<string>('choix objets (0)')
const objetsListeSelect = ref<Objet[]>([])
let demandeInformation: boolean = false
const inpTxtCritere = ref<any>(null)

onMounted(async () => {
    await listeTypeObjet()
    const defaultTypeObjet = typesObjetListe.value.find(item => item.bdefaut === 1)
    if (defaultTypeObjet) {
        idTypeObjet.value = defaultTypeObjet.id
    }
})

const typesObjetListe = ref<TypeObjet[]>([])

const listeTypeObjet = async (): Promise<void> => {
    const response: ApiResponseTOL = await getTypeObjetListe(ssServer.value, ssPageType.value,)
    if (response.success === false) {
        messageErreur.value = response.message
    } else {
        messageErreur.value = ''    
    }
    typesObjetListe.value = response.success && response.data ? response.data : []
    //console.log(typesObjetListe.value)
}

const typesObjetsTous = computed(() => {
    return [
        { id: 0, nom: '-tous-' },
        ...typesObjetListe.value.sort((a, b) => a.nom.localeCompare(b.nom))
    ]
})

const typesObjetsFavoris = computed(() => {
    const toFavoris = typesObjetListe.value.filter(item => item.bfavori === 1)
    return [
        { id: 0, nom: '-tous-' },
        ...toFavoris.sort((a, b) => a.nom.localeCompare(b.nom))
    ]
})

const bFavoriExist: ComputedRef<boolean> = computed(() => {
    if (typesObjetsFavoris.value.length > 1) {
        return true
    } else {
        return false
    }
})
const bTOFavori: ComputedRef<boolean> = computed(() => {
    if (typesObjetsFavoris.value.length > 1 && codeFavoriTous.value === 'favori') {
        return true
    } else {
        return false
    }
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
        recherche()
    }, typingInterval)

    if (inpTxtCritere.value?.$el?.querySelector('input')) {
        inpTxtCritere.value.$el.querySelector('input').focus()
    }
}

const recherche = async (): Promise<void> => {
    const critere: string = txtCritere.value.trim()
    if (critere !== '') {
        const oCritere: CritereRecherche = {
            critere: critere,
            idtype: idTypeObjet.value,
            nombremaximumretour: nombreMaximumRetour.value
        }
        const response: ApiResponseOL = await getObjetsListe(ssServer.value, ssPage.value, JSON.stringify(oCritere))
        if (response.success === false) {
            messageErreur.value = response.message
        }
        const returnListe: Objet[] = response.success && response.data ? response.data : []
        if (returnListe.length < nombreMaximumRetour.value) {
        libelleListe.value = `Choix objets (${returnListe.length})`
        } else {
        libelleListe.value = `Choix objets (${returnListe.length}). Attention, plus de ${nombreMaximumRetour} objets correspondent aux critères`
        }
        objetsListeSelect.value = returnListe
    }
}

watch(() => codeFavoriTous.value, (newValue: string): void => {
    if (newValue === 'favori') {
        idTypeObjet.value = 0
    }
})

watch(() => txtCritere.value, (newValue: string | null): void => {
  if (newValue === null || newValue === '') {
    libelleListe.value = `Selection adresses (0)`
    objetsListeSelect.value = []
  }
})

watch(() => idTypeObjet.value, (): void => {
    if (txtCritere.value.trim() !== '') {
        recherche()
    }
})

const emit = defineEmits<{
  (e: 'choixAutreObjet', id: number, choix: string): void
}>()

const choixAutreObjet = (objet: Objet): void => {
  if (!demandeInformation) {
    emit('choixAutreObjet', objet.id, JSON.stringify(objet))
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