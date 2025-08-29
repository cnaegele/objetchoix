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
        </v-row>
      </v-container>
      <ObjetChoix
        :batiment="choixBatiment"
        :parcelle="choixParcelle"
        :batpar="choixBatPar"
        :rue="choixRue"
      ></ObjetChoix> 
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue' 
import ObjetChoix from '@/components/ObjetChoix.vue'

const choixBatiment = ref<string>('oui')
const choixParcelle = ref<string>('oui')
const choixBatPar = ref<string>('oui')
const choixRue = ref<string>('oui')

  //
</script>
