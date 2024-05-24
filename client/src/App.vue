<template>
    <div class="as-container">
        <div class="container">
            <div class="as-margin-top-space-8 as-margin-bottom-space-8">
                <h1 class="">Send SMS</h1>
            </div>
        </div>
        <div class="as-container buttons container as-margin-bottom-space-6 as-display-flex">
            <button class="as-btn-simple as-margin-right-space-3 as-btn-hover-default btn-with-icon">
                <svg class="as-margin-right-space-1" width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.14286 1C7.32423 1 5.58009 1.72245 4.29412 3.00841C3.00816 4.29437 2.28571 6.03852 2.28571 7.85714H0L2.96381 10.821L3.01714 10.9276L6.09524 7.85714H3.80952C3.80952 4.90857 6.19429 2.52381 9.14286 2.52381C12.0914 2.52381 14.4762 4.90857 14.4762 7.85714C14.4762 10.8057 12.0914 13.1905 9.14286 13.1905C7.67238 13.1905 6.33905 12.5886 5.37905 11.621L4.29714 12.7029C4.93213 13.3413 5.68723 13.8478 6.51891 14.193C7.35058 14.5383 8.24238 14.7154 9.14286 14.7143C10.9615 14.7143 12.7056 13.9918 13.9916 12.7059C15.2776 11.4199 16 9.67577 16 7.85714C16 6.03852 15.2776 4.29437 13.9916 3.00841C12.7056 1.72245 10.9615 1 9.14286 1ZM8.38095 4.80952V8.61905L11.619 10.539L12.2057 9.56381L9.52381 7.97143V4.80952H8.38095Z" fill="#333333"/>
                </svg>                          
                <span>SMS-logg</span>
            </button>
            <button class="as-btn-simple as-margin-right-space-3 as-btn-hover-default btn-with-icon">
                <svg class="as-margin-right-space-1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;">
                    <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"></path>
                </svg>            
                <span>Legg til nyhetssak</span>
            </button>
        </div>
        <div class="as-container container flex-container">
            <div class="flex-container-left">
                <!--Avsender-->
                <div class="as-card-1 as-padding-space-3 margin-bottom"> 
                    <h4>Avsender</h4>
                    <div class="text-align-right">
                        <a>Rediger kontaktpersoner</a>
                    </div>
                    
                    <!--Inputfelt-->
                    <div class="dropdown as-margin-top-space-1"> 
                        <div class="dropdown-left-column">
                            <span class="dropdown-label"></span>
                        </div>
                        <div class="dropdown-right-column">
                            <icon>X </icon>
                        </div>
                    </div>

                    <v-autocomplete variant="outlined" label="Velg avsender"
                    :items="getAvsendere()"
                    ></v-autocomplete>

                    
                    <!--Varsel-->
                    <div class="temporary-notification warning as-margin-top-space-1">
                        <h5>OBS!</h5>
                        <p>Mottakeren kan ikke svare hvis du bruker denne avsenderen.</p>
                    </div>
                    <div class="toggle-container as-margin-top-space-1 ">
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                        <p class="as-margin-right-space-3">Send kopi til avsender</p>
                    </div>
                </div>
                
                <!--Mottakere-->
                <div class="as-card-1 as-padding-space-3 margin-bottom"> 
                    <div class="as-margin-bottom-space-2">
                        <h4>Mottakere</h4>
                    </div>

                    <!--Varsel-->
                    <div class="temporary-notification info as-margin-top-space-1">
                        <h5>Legge til mange mottakere?</h5>
                        <p>Hvis du skal sende SMS til mange deltakere kan det hende du burde gå gjennom rapporter. 
                            <br>
                            <a>Gå til rapporter →</a>
                        </p>
                    </div>

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

                    <p class="as-padding-top-space-1 text-align-right">Totalt {{ mottakere.length }} mottaker{{ mottakere.length != 1 ? 'e' : '' }}</p>

                </div>
                <!--Innhold-->
                <div class="as-card-1 as-padding-space-3 margin-bottom"> 
                    <h4>Innhold</h4>
                    
                    <!--Inputfelt-->
                    <div class="as-margin-top-space-1"> 
                        <v-textarea label="Melding" v-model="textmessage"></v-textarea>
                    </div>
                    <div>
                        <p class="as-padding-top-space-1 text-align-right">Total kostnad: 0.00 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: #333;transform: ;msFilter:;">
                            <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path>
                        </svg></p> 
                    </div>
                </div>

                <button class="as-btn-simple as-btn-simple-primary">Send SMS →</button>

            </div>
            
            <div class="flex-container-right">
                <phoneImg :mobile="getMottakerMobilOnly()" :message="textmessage as string" />
            </div>
            
        </div>
        <div class="as-margin-bottom-space-8"></div>

        <!-- Legg til ny mottaker -->
        <FloatingClosable ref="floatingLeggTilMottaker">
            <div>
                <div class="as-padding-bottom-space-4">
                    <h4 class="nop-impt">Legg til mottaker</h4>
                </div>
                <!-- v-if nyMobil exist on mottakere -->
                <div v-if="mobilExist()" >
                    <PermanentNotification :typeNotification="'danger'" :tittel="'Mobilnummer eksisterer'" :description="'Du har allerede lagt til dette mobilnummeret!'" />
                </div>
                <div>
                    <v-text-field type="tel" :rules="[validateMobileNumber]" v-model="nyMobil" label="Mobil" variant="outlined" required></v-text-field>
                </div>
                <div>
                    <v-text-field v-model="nyMobilNavn" label="Navn" variant="outlined"></v-text-field>
                </div>
                <div class="as-margin-top-space-1">
                    <v-btn @click="addNewMottaker()" variant="tonal">
                        Legg til  
                    </v-btn>
                </div>
            </div>
        </FloatingClosable>

    </div>
</template>

<script lang="ts">
import FirstTab from './tabs/FirstTab.vue';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import { Director } from 'ukm-spa/Director';
import phoneImg from './components/PhoneImgComponent.vue';
import FloatingClosable from './components/FloatingClosable.vue';
import { PermanentNotification } from 'ukm-components-vue3';


var ajaxurl : string = (<any>window).ajaxurl; // Kommer fra global
var alleMottakere : string = (<any>window).alleMottakere; // Definert i PHP
const spaInteraction = new SPAInteraction(null, ajaxurl);



export default {
    data() {
        return {
            name : "World" as String,
            activeTab : 'first' as String,
            textmessage : '' as String,
            avsendere : [] as Array<{mobil : String, name : String}>,
            mottakere : [] as Array<{mobil : String, name : String}>,
            nyMobil : '' as String,
            nyMobilNavn : '' as String,
        }
    },

    components : {
        FirstTab : FirstTab,
        phoneImg : phoneImg,
        FloatingClosable : FloatingClosable,
        PermanentNotification : PermanentNotification

    },

    mounted: function () {
        this.getInitialData();
        if(alleMottakere.length > 0) {
            this.mottakere = (<any>alleMottakere);
        }
    },
    
    methods: {
        openLeggTilMottaker() {
            (<typeof FloatingClosable>this.$refs.floatingLeggTilMottaker).open();
        },
        addNewMottaker() {
            this.nyMobil = this.nyMobil.replace(/\s/g, '');
            this.nyMobilNavn = this.nyMobilNavn.trim();

            if(this.nyMobil.length < 1 || this.nyMobilNavn.length < 1 || this.mobilExist() || !this._validateMobileNumber(this.nyMobil)) {
                return;
            }
            
            // Remove spaces

            this.mottakere.push({mobil: this.nyMobil, name: this.nyMobilNavn});
            this.nyMobil = '';
            this.nyMobilNavn = '';
            (<typeof FloatingClosable>this.$refs.floatingLeggTilMottaker).close();
        },
        async getInitialData() {
            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'getInitialData',
            };

            var response = await spaInteraction.runAjaxCall('/', 'POST', data);
            
            for(var key in response.SMS_avsendere) {
                this.avsendere.push({mobil : key, name : response.SMS_avsendere[key]});
            }
        },
        getAvsendere() {
            var retArr = [];
            for(var avsender in this.avsendere) {
                retArr.push(this.avsendere[avsender].name + ' (' + this.avsendere[avsender].mobil + ')');
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

.dropdown-right-column {
    text-align: left;
    width: 2%;
    height: 100%;
    vertical-align:middle;
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

.alle-mottakere {
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
