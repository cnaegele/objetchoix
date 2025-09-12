<template>
  <v-app>
    <v-main>
      <v-container fluid>
        <v-row>
          <v-col>
            <v-switch 
              :label="`choix bâtiments ${choixBatiment}`" 
              v-model="choixBatiment"
              false-value="oui"
              true-value="non"
            ></v-switch>
          </v-col>
          <v-col>
            <v-switch 
              :label="`choix parcelles ${choixParcelle}`" 
              v-model="choixParcelle"
              false-value="oui"
              true-value="non"
            ></v-switch>
          </v-col>
          <v-col>
            <v-switch 
              :label="`choix bâtiment et parcelles par adresse ${choixBatPar}`" 
              v-model="choixBatPar"
              false-value="oui"
              true-value="non"
            ></v-switch>
          </v-col>
          <v-col>
            <v-switch 
              :label="`choix rue ${choixRue}`" 
              v-model="choixRue"
              false-value="oui"
              true-value="non"
            ></v-switch>
          </v-col>
          <v-col>
            <v-switch 
              :label="`choix autre objet ${choixAutreObjet}`" 
              v-model="choixAutreObjet"
              false-value="oui"
              true-value="non"
            ></v-switch>
          </v-col>
          <v-col>
            <div class="d-flex">
              <v-select
                class="ml-4 mr-2"
                label="type de choix"
                :items="['unique', 'multiple']"
                v-model="modeChoix"
                @click="choixObjet = false"
              ></v-select>
            </div>
          </v-col>
          <v-col>
          <v-btn
              rounded="lg"
              @click="choixObjet = !choixObjet"
              class="text-none text-subtitle-1 text-medium-emphasis"
          >Choix objets</v-btn>
          </v-col>
        </v-row>
      </v-container>
      <ObjetChoix v-if="choixObjet"
        :modeChoix="modeChoix"
        :batiment="choixBatiment"
        :parcelle="choixParcelle"
        :batpar="choixBatPar"
        :rue="choixRue"
        :autre="choixAutreObjet"
        :ssServer="ssServer"
        @choixObjet="receptionObjet"
      ></ObjetChoix> 
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue' 
import ObjetChoix from '@/components/ObjetChoix.vue'

const modeChoix = ref<string>('unique')
const choixBatiment = ref<string>('oui')
const choixParcelle = ref<string>('oui')
const choixBatPar = ref<string>('oui')
const choixRue = ref<string>('oui')
const choixAutreObjet = ref<string>('oui')
const choixObjet = ref<boolean>(false)

const ssServer = ref<string>('')
if (import.meta.env.DEV) {
  ssServer.value = 'https://mygolux.lausanne.ch'    
}

const receptionObjet = (id: number, jsonData: string) => {
  console.log(`Réception objet \njson: ${jsonData}`)
}

  //
</script>
