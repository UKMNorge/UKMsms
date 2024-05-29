<template>
    <div class="as-container container">
        <div class="mobile-entire-div">
            <div class="as-card-2 as-padding-space-2 as-margin-top-space-2 as-display-flex-wrap">
                <div v-for="r in rapports" class="as-chip as-margin-top-space-1 as-margin-right-space-1">
                    {{ r.mottaker }} - {{ r.report }}
                </div>
            </div>
        </div>

    </div>
</template>

<script lang="ts">
import type Avsender from '../objects/Avsender';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';

var ajaxurl : string = (<any>window).ajaxurl; // Kommer fra global
const spaInteraction = new SPAInteraction(null, ajaxurl);


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
        }
    },
    data() {
        return {
            mottakere : this.mottakere,
            selectedAvsender : this.selectedAvsender as Avsender|null, 
            textMsg : this.textMsg,
            rapports : [] as Array<any>,
        }
    },
    methods: {
        async sendSMS() {
            if(this.mottakere.length == 0) {
                throw new Error("Ingen mottakere valgt")
            }
            if(this.textMsg.length == 0) {
                throw new Error("Ingen melding skrevet")
            }
            if(this.selectedAvsender == null || this.selectedAvsender.getTelefonnummer().length == 0) {
                throw new Error("Ingen avsender valgt")
            }


            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'sendSMS',
                avsender: this.selectedAvsender.getTelefonnummer(),
                mottakere: this.mottakere.map((mottaker) => mottaker.mobil),
                message: this.textMsg,
            };

            var response = await spaInteraction.runAjaxCall('/', 'POST', data);

            for(var rItem of response.reports) {
                this.rapports.push(<any>rItem);
            }
        }
    }
}
</script>


<style scoped>

</style>


