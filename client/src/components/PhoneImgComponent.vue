<template>
    <div>
        <div class="mobile-entire-div">
            <div class="mobile-number">
                <span class="mobile-tlf">{{ formattedMobiles().length > 20 ? formattedMobiles().substring(0, 40) + '...' : formattedMobiles() }}</span>
            </div>
            <div class="mobile-message">
                <!-- <span class="mobile-msg">{{ getFormatedMessage() }}</span> -->
                <div v-html="getFormatedMessage()"></div>
            </div>
            <svg class="mobile-svg" width="300" height="610" viewBox="0 0 300 610" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="9.5022" y="5" width="280.995" height="600" rx="32.5792" fill="white" stroke="#22374D" stroke-width="8.1448"/>
                <rect x="0.678733" y="96.629" width="10.8597" height="48.8688" rx="4.75113" fill="#22374D" stroke="#22374D" stroke-width="1.35747"/>
                <rect x="288.462" y="96.629" width="10.8597" height="76.0181" rx="4.75113" fill="#22374D" stroke="#22374D" stroke-width="1.35747"/>
                <rect x="0.678733" y="159.072" width="10.8597" height="48.8688" rx="4.75113" fill="#22374D" stroke="#22374D" stroke-width="1.35747"/>
                <mask id="path-5-inside-1_134_832" fill="white">
                <path d="M13.5745 37.5792C13.5745 21.8353 26.3374 9.07239 42.0813 9.07239H257.918C273.662 9.07239 286.425 21.8353 286.425 37.5792V81.0181H13.5745V37.5792Z"/>
                </mask>
                <path d="M13.5745 37.5792C13.5745 21.8353 26.3374 9.07239 42.0813 9.07239H257.918C273.662 9.07239 286.425 21.8353 286.425 37.5792V81.0181H13.5745V37.5792Z" fill="#F5F5F5"/>
                <path d="M13.5745 9.07239H286.425H13.5745ZM286.425 81.6968H13.5745V80.3393H286.425V81.6968ZM13.5745 81.0181V9.07239V81.0181ZM286.425 9.07239V81.0181V9.07239Z" fill="#E9E9EB" mask="url(#path-5-inside-1_134_832)"/>
                <circle cx="150.945" cy="38.7496" r="18.8174" fill="#22374D"/>
                <mask id="mask0_134_832" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="134" y="22" width="35" height="35">
                <g clip-path="url(#clip0_134_832)">
                <path d="M151.193 41.1154C140.118 41.1154 136.227 46.7277 136.227 50.4691V56.0814H166.159V50.4691C166.159 46.7277 162.267 41.1154 151.193 41.1154Z" fill="#333333"/>
                <path d="M151.192 39.2447C155.842 39.2447 159.611 35.4757 159.611 30.8264C159.611 26.1771 155.842 22.4081 151.192 22.4081C146.543 22.4081 142.774 26.1771 142.774 30.8264C142.774 35.4757 146.543 39.2447 151.192 39.2447Z" fill="#333333"/>
                </g>
                </mask>
                <g mask="url(#mask0_134_832)">
                <circle cx="150.955" cy="38.059" r="18.0223" fill="#F5F5F5"/>
                </g>
                <rect x="28.2791" y="120.385" width="185.785" height="287.783" rx="9.26894" fill="#E9E9EB"/>
                <path d="M28.2826 394.187C28.2826 403.6 25.7171 407.398 24.4343 408.121C29.3239 408.616 31.7536 405.024 32.3573 403.167C32.5384 401.432 29.7163 396.457 28.2826 394.187Z" fill="#E9E9EB"/>
                <path d="M89.5925 594.14H211.764" stroke="#22374D" stroke-width="2.0362" stroke-linecap="round"/>
                <rect x="48.3159" y="558.835" width="224.195" height="18.9365" rx="9.46823" fill="white" stroke="#B4B4B4" stroke-width="0.678733"/>
                <circle cx="30.983" cy="568.11" r="10.621" fill="#E9E9EB"/>
                <path d="M30.2485 572.437V563.783H31.7171V572.437H30.2485ZM26.6558 568.844V567.375H35.3099V568.844H26.6558Z" fill="#B4B4B4"/>
                <defs>
                <clipPath id="clip0_134_832">
                <rect width="33.6733" height="33.6733" fill="white" transform="translate(134.356 22.4081)"/>
                </clipPath>
                </defs>
            </svg>
        </div>

    </div>
</template>

<script lang="ts">


export default {
    props: {
        mobile: {
            type: Array,
            default: () => []
        },
        message: {
            type: String,
            default: ""
        }
    },
    data() {
        return {
     
        }
    },
    methods: {
        formattedMobiles() {
            return this.mobile.join(", ");
        },
        getFormatedMessage() {
            return this.message.replace(/\n/g, '<br>');
        }
    }
}
</script>


<style scoped>
.mobile-entire-div {
    height: 500px !important;
    width: 100% !important;
    position: relative;
    display: flex;
}
.mobile-number {
    background: transparent;
    display: flex;
    height: auto;
    position: absolute;
    top: 44px;
    height: 50px;
    width: 100%;
    left: 0;
    right: 0;
    text-align: center;
}
.mobile-number .mobile-tlf {
    margin: auto;
    font-size: 12px;
}
.mobile-svg {
    margin: auto;
    width: 300px !important;
    height: 610px !important;
}
.mobile-message {
    position: absolute;
    top: 122px;
    left: 48px;
    width: 183px;
    height: 286px;
    overflow-y: auto;
    word-break: break-all;
    padding: var(--initial-space-box);
}
.mobile-message .mobile-msg {
    word-break: break-all;
}
</style>


