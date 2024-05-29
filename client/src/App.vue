<template>
    <div class="as-container">
        <div v-show="!SMSsendt">
            <div class="container">
                <div class="as-margin-top-space-8 as-margin-bottom-space-8">
                    <h1 class="">Send SMS</h1>
                </div>
            </div>
            <div class="as-container buttons container as-margin-bottom-space-6 as-display-flex">
                
                <div class="as-margin-right-space-2">
                    <v-btn
                        class="v-btn-as v-btn-hvit"
                        prepend-icon="mdi-history"
                        color="#000"
                        rounded="large"
                        size="x-large"
                        @click="openSMSLogs()"
                        variant="outlined" >
                        SMS-logg
                    </v-btn>

                    <FloatingClosable ref="floatingLogs">
                        <v-table>
                            <thead>
                                <tr>
                                    <th class="text-left">Tidspunkt</th>
                                    <th class="text-left">Handling</th>
                                    <th class="text-left">Bruker</th>
                                    <th class="text-left">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in alleSMSLogs">
                                    <td>{{ log.time }}</td>
                                    <td>{{ log.action }}</td>
                                    <td>{{ log.user }}</td>
                                    <td>{{ log.description }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </FloatingClosable>

                </div>
                <div class="as-margin-right-space-2">
                    <v-btn
                        class="v-btn-as v-btn-hvit"
                        prepend-icon="mdi-plus"
                        color="#000"
                        rounded="large"
                        size="x-large"
                        @click="openNyhetsaker()"
                        variant="outlined" >
                        Legg til nyhetssak
                    </v-btn>

                    <FloatingClosable ref="floatingLeggTilNyhetsak">
                        <div>
                            <div class="as-padding-bottom-space-3">
                                <h4 class="nop-impt">Legg til mottaker</h4>
                            </div>
                            <p>Hvis du ønsker å dele mer informasjon eller nyheter kan du legge til en lenke til en nyhetssak i meldingen.</p>
                            
                            <div class="as-margin-top-space-4">
                                <v-autocomplete variant="outlined" label="Velg nyhetssak" class="v-autocomplete-arr-sys" :items="alleNyhetsaker" v-model="selectedNyhetssak"></v-autocomplete>
                            </div>

                            <div class="nyhetsaker-buttons as-display-flex">
                                <div class="as-margin-auto">
                                    <v-btn
                                        class="v-btn-as v-btn-bla"
                                        rounded="large"
                                        size="large"
                                        @click="redirectToNewNyhetssak()"
                                        variant="outlined" >
                                        Opprett nyhetssak
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </FloatingClosable>

                </div>
            </div>
            <div class="as-container container flex-container">
                <div class="flex-container-left">
                    <!--Avsender-->
                    <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2 avsender-div"> 
                        <h4>Avsender</h4>
                        <div class="text-align-right">
                            <a>Rediger kontaktpersoner</a>
                        </div>
                    
                    <!-- Avsender -->
                        <!--Inputfelt-->
                        <v-autocomplete variant="outlined" label="Velg avsender" class="v-autocomplete-arr-sys" :items="avsendere" v-model="selectedAvsender"></v-autocomplete>

                        <!--Varsel-->         
                        <PermanentNotification v-if="getSelectedAvsender() != null && !getSelectedAvsender()?.isTelefonnummerValid()" :typeNotification="'warning'" :tittel="'OBS!'" :description="'Mottakeren kan ikke svare hvis du bruker denne avsenderen.'" />

                        
                        <div class="as-display-flex">
                            <div class="as-margin-auto as-margin-right-none">
                                <div class=" as-padding-right-space-2">
                                    <v-switch color="var(--color-primary-bla-600)" v-model="kopiTilAvsender" label="Send kopi til avsender" value="Send kopi til avsender"></v-switch>
                                </div>
                            </div>
                        </div>

                    </div>
                <!-- Nyhetssaker -->
                    <div v-if="selectedNyhetssak" class="as-card-1 as-padding-space-3 as-margin-bottom-space-2"> 
                        <h4>Nyhetssak</h4>

                        <div class="as-margin-top-space-2 alle-nyhetssaker">
                            <div @click="selectedNyhetssak = null" class="as-chip as-margin-top-space-1 as-margin-right-space-1">
                                <p>{{ getSelectedNyhetssak()?.getNavn() }}</p>
                                <button class="icon">
                                    <svg class="remove-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path data-v-36f76f19="" d="M11.5 4.24264L10.0858 2.82843L7.25736 5.65685L4.42893 2.82843L3.01472 4.24264L5.84315 7.07107L3.01472 9.89949L4.42893 11.3137L7.25736 8.48528L10.0858 11.3137L11.5 9.89949L8.67157 7.07107L11.5 4.24264Z" fill="#9B9B9B"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!--Mottakere-->
                    <div class="as-card-1 as-padding-space-3 margin-bottom"> 
                        <div class="as-margin-bottom-space-2 as-display-flex">
                            <h4 class="as-margin-auto as-margin-left-none">Mottakere</h4>

                            <v-btn @click="mottakereInfo = !mottakereInfo" class="vuetify-icon-button as-margin-auto as-margin-right-none" density="compact" icon variant="tonal">
                                <v-icon>mdi-information-slab-symbol</v-icon>
                            </v-btn>
                        </div>
                        <!--Varsel-->
                        <PermanentNotification v-if="mottakereInfo" :typeNotification="'info'" :tittel="'Legge til mange mottakere?'" :description="'Hvis du skal sende SMS til mange deltakere kan det hende du burde gå gjennom rapporter.'" />


                        <!-- liste av mottakere -->
                        <div class="as-card-2 as-padding-space-2 as-margin-top-space-2 nosh-impt as-card-lightest-color">
                            <div class="">
                                <p class="motakkere-overtittel">Mottakere</p>
                            </div>

                            <div class="alle-mottakere">
                                <div @click="removeMottaker(mottaker)" v-for="mottaker in mottakere" class="as-chip as-margin-top-space-1 as-margin-right-space-1">
                                    <p>{{ mottaker.mobil }} ({{ mottaker.name }})</p>
                                    <button class="icon">
                                        <svg class="remove-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path data-v-36f76f19="" d="M11.5 4.24264L10.0858 2.82843L7.25736 5.65685L4.42893 2.82843L3.01472 4.24264L5.84315 7.07107L3.01472 9.89949L4.42893 11.3137L7.25736 8.48528L10.0858 11.3137L11.5 9.89949L8.67157 7.07107L11.5 4.24264Z" fill="#9B9B9B"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Add new mottaker -->
                                <div class="as-chip button-chip as-margin-top-space-1 as-margin-right-space-1">
                                    <button @click="openLeggTilMottaker()" class="icon-button">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 0H4V4H0V6H4V10H6V6H10V4H6V0Z" fill=""/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="as-padding-top-space-1">
                            <p class="text-align-right">Totalt {{ mottakere.length }} mottaker{{ mottakere.length != 1 ? 'e' : '' }}</p>
                        </div>

                    </div>
                    <!--Innhold-->
                    <div class="as-card-1 as-padding-space-3 margin-bottom innhold-div"> 
                        <div class="as-margin-bottom-space-2 as-display-flex">
                            <h4 class="as-margin-auto as-margin-left-none">Innhold</h4>

                            <v-btn @click="loggInfo = !loggInfo" class="vuetify-icon-button as-margin-auto as-margin-right-none" density="compact" icon variant="tonal">
                                <v-icon>mdi-information-slab-symbol</v-icon>
                            </v-btn>
                        </div>
                        <!-- INFO -->
                        <PermanentNotification class="as-margin-bottom-space-2" v-if="loggInfo" :typeNotification="'info'" :tittel="'OBS: Alle SMS logges og lagres!'" 
                        :description="'SMS som sendes med dette systemet går fra UKM-Norges konto hos tjenesteleverandøren. UKM-Norge er juridisk ansvarlig for innholdet, og vi logger og lagrer derfor alle meldinger slik at eventuelle misbruk av systemet til personangrep, spam, eller private formål kan oppdages og spores tilbake til avsender. Sendingsloggen brukes også som fakturagrunnlag i de tilfeller UKM Norge finner det nødvendigå fakturere for forbruk utover gratiskvoten.'" />
                        <!--Inputfelt-->
                        <div class="as-margin-top-space-1"> 
                            <v-textarea class="vue-as-textarea" label="Melding" v-model="textmessage"></v-textarea>
                        </div>                    

                    
                        <div class="as-display-flex sms-info as-margin-bottom-space-2">
                            <div class="as-margin-auto as-display-flex as-margin-right-none">
                                <v-chip class="as-margin-right-space-1" density="compact" size="small">
                                    {{ getTextmessage().length }} tegn
                                </v-chip>
                                <v-chip class="as-margin-right-space-1" density="compact" size="small">
                                    {{ Math.floor(getTextmessage().length / 160) + 1 }} SMS
                                </v-chip>
                                <v-chip class="as-margin-right-space-1" density="compact" size="small">
                                    {{ (getPrice()).toLocaleString('no-NO', { minimumFractionDigits: 2 }) }} kr
                                </v-chip>
                            </div>
                        </div>

                        <div v-if="getTextmessage().length > 480">
                            <PermanentNotification :typeNotification="'info'" :tittel="'Har du mye på hjertet?'" :description="'Hvis du har behov for å sende mye informasjon kan det hende du heller burde lenke til en nyhetssak i meldingen din. Da er det enklere for mottakerne å lese meldingen i tillegg til at det blir billigere å sende SMS-en.'" />
                        </div>


                        <div class="as-display-flex">
                            <div class="as-margin-auto as-margin-right-none">
                                <div class="">
                                    <v-switch color="var(--color-primary-bla-600)" v-model="ukmSignatur" label="UKM signatur" value="UKM signatur"></v-switch>
                                </div>
                            </div>
                        </div>


                        

                    </div>

                <div class="as-display-flex as-margin-top-space-4">
                    <div class="as-margin-auto as-margin-right-none">
                        <v-btn
                            class="v-btn-as v-btn-success"
                            rounded="large"
                            size="large"
                            @click="sendSMS()"
                            variant="outlined"
                            :disabled="!isSendingReady()">
                            Send SMS →
                        </v-btn>
                    </div>
                </div>

                </div>
                
                <div class="flex-container-right">
                    <phoneImg :mobile="getMottakerMobilOnly()" :message="getTextmessage()" />
                </div>
                
            </div>
            <div class="as-margin-bottom-space-8"></div>

            <!-- Legg til ny mottaker -->
            <FloatingClosable ref="floatingLeggTilMottaker">
                <div class="mottakere-tabs as-display-flex">
                    <div class="mottakere-tabs-under nop">
                        <v-tabs align-tabs="center" v-model="tab">
                            <v-tab text="Ny mottaker"></v-tab>
                            <v-tab text="Mottaker fra innslag"></v-tab>
                        </v-tabs>
                        
                        <div class="as-margin-top-space-4">
                            <v-tabs-window v-model="tab">
                                <!-- Legg til ny mottaker TAB WINDOW ITEM -->
                                <v-tabs-window-item>
                                    <div class="as-padding-bottom-space-3">
                                        <h4 class="nop-impt">Legg til mottaker</h4>
                                    </div>
                                    <!-- v-if nyMobil exist on mottakere -->
                                    <div v-if="mobilExist()" >
                                        <PermanentNotification class="as-margin-bottom-space-2" :typeNotification="'danger'" :tittel="'Mobilnummer eksisterer'" :description="'Du har allerede lagt til dette mobilnummeret!'" />
                                    </div>
                                    <div>
                                        <InputTextOverlay :placeholder="'Navn'" v-model="nyMobilNavn" />
                                    </div>
                                    <div class="as-margin-top-space-2">
                                        <InputTextOverlay :placeholder="'Mobiltelefonnummer'" :type="'tel'" v-model="nyMobil" />
                                    </div>
                                    <div class="as-margin-top-space-2">
                                        <v-btn
                                            class="v-btn-as v-btn-success"
                                            rounded="large"
                                            size="large"
                                            @click="addNewMottaker()"
                                            variant="outlined">
                                            Legg til
                                        </v-btn>
                                    </div>
                                </v-tabs-window-item>

                                <!-- Legg til mottaker fra innslag TAB WINDOW ITEM -->
                                <v-tabs-window-item>
                                    <div class="as-margin-top-space-2">
                                        <div v-for="innslag in getInnslagMottakere()">
                                            <div class="as-card-2 as-padding-space-2 nosh-impt as-margin-bottom-space-2 as-card-lightest-color" v-if="innslag.length > 0">
                                                <div>
                                                    <h5 class="innslag-navn-mottakere">{{ innslag[0].innslagNavn }}</h5>
                                                    <span class="innslag-type-mottakere">{{ innslag[0].innslagType }}</span>
                                                </div>
                                                <div class="alle-innslag-mottakere">
                                                    <template v-for="person in innslag">
                                                        <div @click="leggTilMottakerFraInnslag(person)" v-show="!isPersonInMottakere(person)" class="as-chip as-margin-top-space-1 as-margin-right-space-1">
                                                            <p>{{ person.mobil }} ({{ person.navn }})</p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </v-tabs-window-item>

                            </v-tabs-window>
                        </div>
                    </div>
                </div>
            </FloatingClosable>
        </div>
        <div v-show="SMSsendt" class="rapport">
            <SendSMSraport :mottakere="mottakere" :selectedAvsender="getSelectedAvsender()" :textMsg="getTextmessage()" ref="sendingRapport" />
        </div>
        {{ SMSsendt }}
    </div>
</template>

<script lang="ts">
import FirstTab from './tabs/FirstTab.vue';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import { Director } from 'ukm-spa/Director';
import phoneImg from './components/PhoneImgComponent.vue';
import SendSMSraport from './components/SendSMSraport.vue';
// import FloatingClosable from './components/FloatingClosable.vue';
import { PermanentNotification } from 'ukm-components-vue3';
import { FloatingClosable } from 'ukm-components-vue3';
import { InputTextOverlay } from 'ukm-components-vue3';


import Avsender from './objects/Avsender';
import Nyhetsak from './objects/Nyhetsak';
import InnslagMottaker from './objects/InnslagMottaker';
import Log from './objects/Log';


var ajaxurl : string = (<any>window).ajaxurl; // Kommer fra global
var alleMottakere : string = (<any>window).alleMottakere; // Definert i PHP
const spaInteraction = new SPAInteraction(null, ajaxurl);



export default {
    data() {
        return {
            SMSsendt : false as Boolean,
            name : "World" as String,
            activeTab : 'first' as String,
            textmessage : '' as String,
            avsendere : [] as Array<Avsender>,
            mottakere : [] as Array<{mobil : String, name : String}>,
            nyMobil : '' as any,
            nyMobilNavn : '' as any,
            mottakereInfo : false as Boolean,
            selectedAvsender : null as Avsender | null,
            selectedNyhetssak : null as any | Nyhetsak,
            alleNyhetsaker : [] as Array<Nyhetsak>,
            kopiTilAvsender : false as Boolean,
            ukmSignatur : false as Boolean,
            aktivMottakerTab : 0 as Number,
            tab : null as any,
            innslagMottakere : [] as Array<Array<InnslagMottaker>>,
            loggInfo : false as Boolean,
            alleSMSLogs : [] as Array<Log>,
        }
    },

    components : {
        FirstTab : FirstTab,
        phoneImg : phoneImg,
        FloatingClosable : FloatingClosable,
        PermanentNotification : PermanentNotification,
        InputTextOverlay : InputTextOverlay,
        SendSMSraport : SendSMSraport,


    },

    mounted: function () {
        this.getInitialData();
        if(alleMottakere.length > 0) {
            this.mottakere = (<any>alleMottakere);
        }
    },
    watch: {
    selectedNyhetssak(newValue : any, oldValue : any) {
        this.closeNyhetssak();
        }
    },
    methods: {
        openLeggTilMottaker() {
            (<typeof FloatingClosable>this.$refs.floatingLeggTilMottaker).open();
        },
        openLogs() {
            (<typeof FloatingClosable>this.$refs.floatingLogs).open();
        },
        openNyhetsaker() {
            (<typeof FloatingClosable>this.$refs.floatingLeggTilNyhetsak).open();
        },
        closeNyhetssak() {
            (<typeof FloatingClosable>this.$refs.floatingLeggTilNyhetsak).close();
        },
        addNewMottaker() {
            this.nyMobil = this.nyMobil.replace(/\s/g, '');
            this.nyMobilNavn = this.nyMobilNavn.trim();

            if(this.nyMobil.length < 1 || this.nyMobilNavn.length < 1 || this.mobilExist() || !this._validateMobileNumber(this.nyMobil)) {
                return;
            }
            
            this.mottakere.push({mobil: this.nyMobil, name: this.nyMobilNavn});
            this.nyMobil = '';
            this.nyMobilNavn = '';
            (<typeof FloatingClosable>this.$refs.floatingLeggTilMottaker).close();
        },
        async getInitialData() {
            this._fetchNyhetsaker();

            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'getInitialData',
            };


            var response = await spaInteraction.runAjaxCall('/', 'POST', data);
            
            for(var key in response.SMS_avsendere) {
                this.avsendere.push(new Avsender(response.SMS_avsendere[key], key));
            }
        },
        async _fetchSMSLog() {
            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'getSMSLog',
            };

            var response = await spaInteraction.runAjaxCall('/', 'POST', data);

            for(var log of response.logs) {
                this.alleSMSLogs.push(new Log(log.time, log.action, log.user, log.description));
            }
        },
        async _fetchInnslag() {
            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'getInnslagMottakere',
            };

            var response = await spaInteraction.runAjaxCall('/', 'POST', data);

            for(var key in response.personer) {
                var innslagArr = [];

                for(var innslag of (<any>response).personer[key]) {
                    innslagArr.push(new InnslagMottaker(innslag.navn, innslag.mobil, innslag.innslagNavn, innslag.innslagType));
                }
                this.innslagMottakere.push(innslagArr);
            }

            this.innslagMottakere = response.personer;
        },
        async _fetchNyhetsaker() {
            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'getNyhetsakerJson',
            };

            var response = await spaInteraction.runAjaxCall('/', 'POST', data);


            for(var n of response.posts) {
                this.alleNyhetsaker.push(new Nyhetsak(n.id, n.title, n.content, n.link));
            }
        },
        openSMSLogs() {
            this.openLogs();
            if(this.alleSMSLogs.length < 1) {
                this._fetchSMSLog();
            }
            return this.alleSMSLogs;
        },
        leggTilMottakerFraInnslag(mottaker : InnslagMottaker) {
            this.mottakere.push({mobil: mottaker.mobil, name: mottaker.navn});
        },
        getInnslagMottakere() {
            if(this.innslagMottakere.length < 1) {
                this._fetchInnslag();
            }
            return this.innslagMottakere;
        },
        getAvsendere() {
            var retArr = [];
            for(var avsender in this.avsendere) {
                retArr.push(this.avsendere[avsender].getTelefonnummer() + ' (' + this.avsendere[avsender].getNavn() + ')');
            }
            
            return retArr;
        },
        getNyhetsaker() {
            var retArr = [];
            for(var nyhetsak of this.alleNyhetsaker) {
                retArr.push(nyhetsak.getStringFormat());
            }
            return retArr;
        },
        getMottakerMobilOnly() : Array<String> {
            return this.mottakere.map((mottaker) => mottaker.mobil);
        },
        mobilExist() : boolean {
            this.nyMobil = this.nyMobil.replace(/\s/g, '');
            return this.mottakere.filter((mottaker) => mottaker.mobil == this.nyMobil).length > 0;
        },
        validateMobileNumber(value : any) {
            return this._validateMobileNumber(value) || 'Sett inn et gyldig mobilnummer';
        },
        _validateMobileNumber(value : any) : boolean {
            const mobileNumberPattern = /^\d{1,8}$/;
            return mobileNumberPattern.test(value);
        },
        removeMottaker(mottaker : {mobil : String, name : String}) {
            this.mottakere = this.mottakere.filter((m) => m.mobil != mottaker.mobil);
        },
        getSelectedAvsender() : Avsender | null {
            if(this.selectedAvsender == null) {
                return null;
            }
            
            return this.selectedAvsender;
        },
        getSelectedNyhetssak() : Nyhetsak | null {
            if(this.selectedNyhetssak == null) {
                return null;
            }
        
            return this.selectedNyhetssak;
        },
        getTextmessage() : string {
            var retMsgString: string = String(this.textmessage);

            if(this.selectedNyhetssak != null) {
                retMsgString += '\n' + this.getSelectedNyhetssak()?.getLink();
            }
            if(this.ukmSignatur) {
                retMsgString += '\n\n-UKM';
            }
            var selAvsender = this.getSelectedAvsender();
            if(selAvsender != null && !selAvsender.isTelefonnummerValid()) {
                retMsgString += '\n(denne SMS kan ikke besvares)';
            }

            return retMsgString;
        },
        redirectToNewNyhetssak() {
            window.location.href = '/wp-admin/post-new.php';
        },
        isPersonInMottakere(person : InnslagMottaker) {
            return this.mottakere.filter((mottaker) => mottaker.mobil == person.mobil).length > 0;
        },
        getPrice() : Number{
            return ((((Math.floor(this.getTextmessage().length / 160) + 1) * 0.4) * this.mottakere.length) + (this.kopiTilAvsender ? 0.4 : 0));
        },
        isSendingReady() : boolean {
            return this.mottakere.length > 0 && this.getTextmessage().length > 0 && this.getSelectedAvsender() != null;
        },
        sendSMS() {
            this.SMSsendt = true;
            (<typeof SendSMSraport>this.$refs.sendingRapport).sendSMS();
        }
    }
}
</script>


<style scoped>
.flex-container {
    display: flex;
}


.flex-container-left {
    width: 70%;
    margin-right: 24px;
}

.flex-container-right {
    width: 30%;
}

.margin-bottom {
    margin-bottom:16px;
}

.text-align-right {
    width: 100%;
    text-align: right;
}
.vuetify-icon-button {
    margin-top: -4px;
}

.temporary-notification {
    width: 100%;
    border: 2px solid;
    border-radius: var(--radius-minimal);
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Varslinger */
.warning {
    background-color: var(--as-color-primary-warning-lightest);
    border-color: var(--as-color-primary-warning-light);
}

.info {
    background-color: var(--as-color-primary-info-lightest);
    border-color: var(--as-color-primary-info-light);
}


.dropdown {
    background-color: var(--color-primary-grey-lightest);
    border-radius: var(--radius-normal);
    padding: 8px 16px;
    display: flex;
}

.dropdown-left-column {
    display: flex;
    flex-direction: column;
    width: 98%;
}

.dropdown-label {
    font-size: 10px;
    font-weight: 400;
    color: #656F7C;
}

.dropdown-input {
    font-size: 16px;
    font-weight: 300;
    color: #1A202C;
}
.avsender-div, .innhold-div {
    padding-bottom: 0 !important;
}
.innslag-type-mottakere {
    font-size: 12px;
    font-weight: 100;
}
.sms-info {
    margin-top: -10px;
}

.toggle-container{
    display: flex;
    width: 100%;
}

/* toggle */
.switch {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 16px;
  }
  
  /* Hide default HTML checkbox */
  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  
  /* The slider */
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--color-primary-grey-light);
    -webkit-transition: .4s;
    transition: .4s;
  }
  
  .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    background-color: var(--color-primary-grey-dark);
    -webkit-transition: .4s;
    transition: .4s;
  }
  
  input:checked + .slider {
    background-color: var(--as-color-primary-primary-lighter);
  }
  
 
  
  input:checked + .slider:before {
    -webkit-transform: translateX(16px);
    -ms-transform: translateX(16px);
    transform: translateX(16px);
    background-color: var(--as-color-primary-primary-darker);
  }
  
  /* Rounded sliders */
  .slider.round {
    border-radius: 16px;
  }
  
  .slider.round:before {
    border-radius: 50%;
  }


/* Phone preview */
#phone-preview {
    /* background-image:url(./img/Phone-illustration.svg); */
    background-repeat: no-repeat;
    height: 611px;
    width: 300px;
    padding-top: 125px;
    padding-left: 25px;
}

#phone-message {
    width: 50px;
    height: 200px;
    overflow-y: auto;
}
.mottakere-tabs {
    width: 100%;
    width: 500px;
}
.mottakere-tabs .mottakere-tabs-under {
    width: 100%;
}
.node-floating-selector {
    margin: auto;
    min-width: 300px;
    max-width: 600px;
    position: relative;
    max-height: 80vh;
    overflow: auto
}

.node-floating-selector-2 {
    margin: auto;
    min-width: 300px;
    max-width: 940px;
    position: relative;
    max-height: 80vh;
    overflow: auto
}

.alle-mottakere, .alle-nyhetssaker, .alle-innslag-mottakere {
    display: flex;
    flex-wrap: wrap;
}
.motakkere-overtittel {
    font-size: 13px;
    font-weight: 200;
}

td {
    vertical-align: top;
    font-size: 13px;
}

tr {
    border-bottom: 1px solid #DDD;
    padding-top: 4px;
    padding-bottom: 4px;
    column-gap: 8px;
}
</style>
