<template>
    <div>
        <v-fade-transition>
            <div v-if="!answered" class="as-container container">
                <template v-if="!savingAnswer">
                    <Feedback :question="'Hvordan vil du vurdere opplevelsen din med den nye SMS-modulen?'" :answers="getFeedbackAnswers()" :campaign="campaignId" :sendAnswerCallback="saveFeedback" />
                </template>
                <template v-else>
                    <div v-if="savingAnswer" class="as-card-1 as-padding-space-6">
                        <h4 class="as-text-center">Takk for din tilbakemelding!</h4>
                    </div>
                </template>
            </div>
        </v-fade-transition>
    </div>
</template>

<script lang="ts">
import { Feedback } from 'ukm-components-vue3';


export default {
    mounted() {
        this._fetchFeedback(this.campaignId);
    },
    components : {
        Feedback : Feedback
    },
    data() {
        return {
            campaignId : 1,
            spaInteraction : (<any>window).spaInteraction,
            answered : true,
            savingAnswer : false,
        }
    },
    methods: {
        async _fetchFeedback(campaignId : number) {
            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'hasUserAnsweredFeedback',
                campaignId: campaignId,
            };

            var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            this.answered = response.hasAnswered ? true : false;
        },
        getFeedbackAnswers() : Array<{id : String, text : String, iconClass : String}> {
            return [
                {id: '1', text: '', iconClass: 'mdi-emoticon-angry-outline'},
                {id: '2', text: '', iconClass: 'mdi-emoticon-sad-outline'},
                {id: '3', text: '', iconClass: 'mdi-emoticon-neutral-outline'},
                {id: '4', text: '', iconClass: 'mdi-emoticon-happy-outline'},
                {id: '5', text: '', iconClass: 'mdi-emoticon-excited-outline'},
            ];
        },
        saveFeedback(answer : {id : String, text : String, iconClass : String}, question : String, campaignId : String) {
            // Save to server
            var data : any = {
                action: 'UKMSMS_ajax',
                SMSaction: 'saveFeedback',
                campaignId: campaignId,
                question : question,
                answerId : answer.id,
                answerText : answer.text,
            };

            this.spaInteraction.runAjaxCall('/', 'POST', data, true); // Silent true
            
            this.savingAnswer = true;
            setTimeout(() => {
                // this.savingAnswer = false;
                this.answered = true;
            }, 3000)
        }
    }
}
</script>


<style scoped>

</style>


