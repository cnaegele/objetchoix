<template>
    <div class="d-flex align-items-baseline">
        <span class="me-2 mt-3">critère selon :</span>
        <v-radio-group v-model="typeCritere" inline>
        <v-radio label="nom" value="nom"></v-radio>
        <v-radio label="n° ECA" value="eca"></v-radio>
        <v-radio label="egid" value="egid"></v-radio>
        </v-radio-group>            
    </div>  
    <v-text-field
        dense
        v-model="txtCritere"
        ref="inpTxtCritere"
        :label="libelleInpCritere"
        style="width: 400px;"
        :rules="critereRule"
        @input="onInputCritere"
    ></v-text-field> 

</template>

<script setup lang="ts">
import type { Batiment, ApiResponseBL } from '@/axioscalls.js'
import { ref, type Ref, watch } from 'vue'
import { getBatimentsListe } from '@/axioscalls.js'

interface Props {
  modeChoix?: string
  typeCritere?: string
  nombreMaximumRetour?: number
  ssServer?: string
  ssPage?: string
}

interface CritereRecherche {
  critere: string;
  crtype: string;
  nombremaximumretour: number;
}

const props = withDefaults(defineProps<Props>(), {
  modeChoix: 'unique',
  typeCritere: 'nom',
  nombreMaximumRetour: 100,
  ssServer: '',
  ssPage: '/goeland/batiment/axios/batiment_liste.php'
})

type ValidationRule = (value: string) => string | boolean
const typeCritereInitial = props.typeCritere
const typeCritere = ref<string>(typeCritereInitial)
const nombreMaximumRetour = ref<number>(props.nombreMaximumRetour)
const txtCritere = ref<string>('')
const libelleInpCritere = ref<string>('')
switch (typeCritereInitial) {
    case 'nom':
      libelleInpCritere.value = 'nom bâtiment'
      break
    case 'eca':
      libelleInpCritere.value = 'n° ECA'
      break
    case 'egid':
      libelleInpCritere.value = 'egid'
     break
    default:
      // Gestion du cas par défaut pour la sécurité
      libelleInpCritere.value = '???'
      break
}
const ssServer = ref<string>(props.ssServer)
const ssPage = ref<string>(props.ssPage)





const inpTxtCritere = ref<any>(null)
let bCritereEgidOK: boolean = true

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
  }}

const prepareRecherche = (): void => {
  const critere: string = txtCritere.value.trim()
  const typeCr: string = typeCritere.value
  if(critere !== '') {
    switch (typeCr) {
      case 'nom':
        if (critere.length >= 3) {
          recherche('nom', critere, nombreMaximumRetour.value)  
        }
        break
      case 'eca':
        recherche('eca', critere, nombreMaximumRetour.value)  
      break
      case 'egid':
        if (bCritereEgidOK) {
          recherche('egid', critere, nombreMaximumRetour.value)
        }
        break
    }
  }
}

const recherche = async (crType: string, critere: string, nombreMaximumRetour: number): Promise<void> => {
    const oCritere: CritereRecherche = {
        crtype: crType,
        critere: critere,
        nombremaximumretour: nombreMaximumRetour
    }
  const response: ApiResponseBL = await getBatimentsListe(ssServer.value, ssPage.value, JSON.stringify(oCritere))
  const acteursListe: Batiment[] = response.success && response.data ? response.data : []

}

const critereRule: ValidationRule[] = [
  (value: string): string | boolean => {
    if (value !== '') {
      if (typeCritere.value === 'egid') {
        if (!/^\+?(0|[1-9]\d*)$/.test(value)) {
          bCritereEgidOK = false
          return `l'egid doit être numérique`
        }
        
        const numericValue = parseInt(value, 10)
        if (numericValue <= 0 || numericValue > 9999999999) {
          bCritereEgidOK = false
          return `l'egid doit être positif et 10 chiffres au maximum`
        }
      }
    }
    bCritereEgidOK = true
    return true
  }
]

watch(() => typeCritere.value, (newValue: string): void => {
  switch (newValue) {
    case 'nom':
      libelleInpCritere.value = 'nom bâtiment'
      break
    case 'eca':
      libelleInpCritere.value = 'n° ECA'
      break
    case 'egid':
      libelleInpCritere.value = 'egid'
      inpTxtCritere.value.validate()
      break
    default:
      // Gestion du cas par défaut pour la sécurité
      libelleInpCritere.value = '???'
      break
  }
  prepareRecherche()
})
</script>

<style scoped>

</style>