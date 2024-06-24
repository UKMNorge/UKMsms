<template>
    <div class="as-container container">        
        <div v-if="errorSend.length > 0">
            <permanent-notification tittel="Feil" description="Kunne ikke sende SMS, kontakt support@ukm.no" typeNotification="danger" />
        </div>
        <div v-else-if="!sendingComplete">
            <div class="as-card-1 as-padding-space-4 as-margin-top-space-2 as-display-flex">
                <div class="as-margin-auto">
                    <h4>Vennligst vent mens SMS sendes</h4>
                    <div class="as-display-flex as-margin-top-space-3">
                        <v-progress-circular :size="113" :width="12" class="as-margin-auto" :model-value="getSendingPercentage()"></v-progress-circular>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="gotRepport" class="mobile-entire-div">
            <div v-if="allSMSSuccess()">
                <permanent-notification tittel="SMS Sendt" description="SMS er sendt til alle mottakere" typeNotification="primary" />
            </div>
            <div v-else class="as-card-1 as-padding-space-2 as-margin-top-space-2">
                <div v-for="r in rapports" :class="r.report == -1 ? 'success-repport' : 'error-repport'" class="as-card-2 nosh-impt as-padding-space-2 as-margin-space-2">
                    <p>{{ getMottakerByMobil(r.mottaker) }} ({{ r.mottaker }}) - <b>{{ r.report == -1 ? 'SMS-en er sendt' : r.report }}</b></p>
                </div>
            </div>
        </div>
        
        <div class="as-margin-top-space-4">
            <v-btn
                class="v-btn-as v-btn-hvit as-margin-right-space-2"
                prepend-icon="mdi-keyboard-backspace"
                color="#000"
                rounded="large"
                size="x-large"
                @click="goBack()"
                :disabled="gotRepport == false"
                variant="outlined" >
                Tilbake til SMS
            </v-btn>
            <v-btn
                class="v-btn-as v-btn-hvit"
                prepend-icon="mdi-history"
                color="#000"
                rounded="large"
                size="x-large"
                @click="openSMSLogg()"
                :disabled="gotRepport == false"
                variant="outlined" >
                SMS-logg
            </v-btn>
        </div>



    </div>
</template>

<script lang="ts">
import type Avsender from '../objects/Avsender';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import { PermanentNotification } from 'ukm-components-vue3';


var ajaxurl : string = (<any>window).ajaxurl; // Kommer fra global
const spaInteraction = new SPAInteraction(null, ajaxurl);

// Define interaction object on ukm-compontents-vue
// Use it here and add it to SPAInteraction(HERE, ajaxurl)

export default {
    props: {
        mottakere: {
            type: Array<{mobil : String, name : String}>,
            required: true
        },
        selectedAvsender: {
            type: Object as () => Avsender | null,
            default: null
        },
        textMsg: {
            type: String,
            required: true
        },
        callbackLogg: {
            type: Function,
            default: () => {}
        }
    },
    data() {
        return {
            mottakere : this.mottakere,
            selectedAvsender : this.selectedAvsender as Avsender|null, 
            textMsg : this.textMsg,
            rapports : [] as Array<any>,
            gotRepport : false as Boolean,
            callbackLogg : this.callbackLogg,
            errorSend : '' as String,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            sendingComplete : false as Boolean,
            initialAntallMottakere : 0,
        }
    },
    components: {
        PermanentNotification
    },
    methods: {
        getSendingPercentage() {
            return Math.round(100-(this.mottakere.length / this.initialAntallMottakere) * 100);
        },
        async sendSMS() {
            if(this.mottakere.length == 0) {
                throw new Error("Ingen mottakere valgt")
            }
            if(this.textMsg.length == 0) {
                throw new Error("Ingen melding skrevet")
            }

            this.initialAntallMottakere = this.mottakere.length;

            // Send 10 SMS at a time
            var mottakere = this.mottakere;
            var mottakereChunk = [];
            while(mottakere.length > 0) {
                mottakereChunk = mottakere.splice(0, 10);
                var result = await this._sendSMS(mottakereChunk);
                if(result) {
                    mottakereChunk = [];
                }
                if(mottakere.length == 0) {
                    this.sendingComplete = true;
                }
            }
        },
        async _sendSMS(mottakerChunk : Array<{mobil : String, name : String}>) {
            if(this.selectedAvsender == null || this.selectedAvsender.getTelefonnummer().length == 0) {
                throw new Error("Ingen avsender valgt")
            }

            console.log('sending til', mottakerChunk.map((mottaker) => mottaker.mobil));

            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'sendSMS',
                avsender: this.selectedAvsender.getTelefonnummer(),
                mottakere: mottakerChunk.map((mottaker) => mottaker.mobil),
                message: this.textMsg,
            };

            try {
                var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            }catch(e : any) {
                console.warn(e);
                this.errorSend = e.message;
                return;
            }

            if(response) {this.gotRepport = true};

            for(var rItem of response.reports) {
                this.rapports.push(<any>rItem);
            }

            return response;
        },
        allSMSSuccess() : Boolean {
            if(this.gotRepport) {
                for(var r of this.rapports) {
                    console.warn(r.report);
                    if(r.report != -1) {
                        return false;
                    }
                }
            }
            else {
                return false;
            }

            return true;
        },
        getMottakerByMobil(mobil : String) : String {
            for(var m of this.mottakere) {
                if(m.mobil == mobil) {
                    return m.name;
                }
            }
            return "";
        },
        goBack() {
            // Refresh the page
            location.reload();
        },
        openSMSLogg() {
            if(this.callbackLogg != null) {
                this.callbackLogg();
            }
        }

        
    }
}
</script>


<style scoped>
.error-repport {
    background: var(--as-color-primary-danger-lightest);
}
.success-repport {
    background: var(--as-color-primary-success-lightest);
}
</style>


