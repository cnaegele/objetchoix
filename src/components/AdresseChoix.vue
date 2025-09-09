<template>
    <v-radio-group
        label="recherche adresse selon"
        v-model="typeCritere"
        inline
        density="compact">
        <v-radio label="début nom principal rue, numéro" :value="'nom'" class="me-4"></v-radio>
        <v-radio label="adresse complète, numéro" :value="'nomcomplet'"></v-radio>
    </v-radio-group>
    <v-text-field
        dense
        clearable
        v-model="txtCritere"
        ref="inpTxtCritere"
        label="adresse"
        class="flex-0-0"
        style="width: 400px; min-width: 400px;"
        @input="onInputCritere"
    ></v-text-field>
      
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Props {
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
  typeCritere: 'nom',
  nombreMaximumRetour: 100,
  ssServer: '',
  ssPage: '/goeland/adresse/axios/adresse_liste.php'
})

const nombreMaximumRetour = ref<number>(props.nombreMaximumRetour)

const typeCritere = ref<string>('nom')
const txtCritere = ref<string>('')
const inpTxtCritere = ref<any>(null)




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
        const apresNumero: string = critere.substring(posi+numero.length).trim()
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
    console.log(oCritere)
    //const response: ApiResponseOL = await getBatimentsListe(ssServer.value, ssPage.value, JSON.stringify(oCritere))
    //const returnListe: Objet[] = response.success && response.data ? response.data : []
    //if (returnListe.length < nombreMaximumRetour) {
    //    libelleListe.value = `Choix bâtiments (${returnListe.length})`
    //} else {
    //    libelleListe.value = `Choix bâtiments (${returnListe.length}). Attention, plus de ${nombreMaximumRetour} bâtiments correspondent aux critères`
    //}
    //batimentsListeSelect.value = returnListe
}

</script>

<style scoped>

</style>