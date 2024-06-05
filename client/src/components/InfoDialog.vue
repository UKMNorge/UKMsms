<template>
    <div>
        <v-dialog max-width="500">
            <template v-slot:activator="{ props: activatorProps }">
                <v-btn
                    text="Retningslinjer"
                    v-bind="activatorProps"
                    class="v-btn-as v-btn-hvit as-margin-right-space-2 "
                    rounded="large"
                    size="large"
                    variant="outlined">
                </v-btn>
            </template>
        
            <template v-slot:default="{ isActive }">
                <v-card class="as-card-1" title="Viktig informasjon">
                    <v-card-text>
                        <div class="as-container">
                            <div class="as-margin-top-space-2 as-margin-bottom-space-1">
                                <h5>OBS: Alle SMS logges og lagres!</h5>
                            </div>
                            <p>SMS som sendes med dette systemet går fra UKM-Norges konto hos tjenesteleverandøren. UKM-Norge er juridisk ansvarlig for innholdet, og vi logger og lagrer derfor alle meldinger slik at eventuelle misbruk av systemet til personangrep, spam, eller private formål kan oppdages og spores tilbake til avsender. Sendingsloggen brukes også som fakturagrunnlag i de tilfeller UKM Norge finner det nødvendigå fakturere for forbruk utover gratiskvoten.</p>

                            <div class="as-margin-top-space-4 as-margin-bottom-space-1">
                                <h5>Kostnad</h5>
                            </div>
                            <p>Alle SMS belastes UKM Norge, uansett hvilken avsender som velges. Alle får 400 tekstmeldinger gratis, og alt forbruk utover dette kan bli fakturert sammen med neste års materiellpakke. 1 SMS = kr. 0,40</p>
                            
                            <div class="as-margin-top-space-4 as-margin-bottom-space-1">
                                <h5>Sende SMS til målgruppe</h5>
                            </div>
                            <p>Når du skal sende SMS til mange deltakere, kan det være nyttig å gå gjennom <a class="as-btn-ahref as-btn-hover-default" href="wp-admin/admin.php?page=UKMrapporter">rapporter</a>. Ved å velge rikgtig målgruppe på rapporter, kan du sortere, filtrere og velge spesifikke mottakere basert på relevante kriterier, og dermed sikre at meldingene dine når riktig målgruppe.</p>
                        </div>
                    </v-card-text>
            
                    <v-card-actions>
                    <v-spacer></v-spacer>
            
                    <v-btn
                        text="Lukk"
                        @click="isActive.value = false"
                    ></v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script lang="ts">


export default {

}
</script>


<style scoped>

</style>


