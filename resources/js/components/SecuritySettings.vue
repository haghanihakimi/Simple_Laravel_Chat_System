<template>
    <div class="ss-container">
        <div class="ss-box">
            <h2 class="settingsHeader">Security Settings</h2>

            <!-- A form to change email address. It contains two input boxes -->
            <form action="/" method="POST" @submit.prevent="saveEmail" enctype="multipart/form-data">
                <div class="formInputs">
                    <label for="emailAddress">Email Address</label>
                    <input type="email" id="emailAddress" :style="[pendingFetch ? {opacity: 0.5} : {opacity:1}]" :disabled="pendingFetch" v-model="userInputs.email" name="email" placeholder="e.g example@email.com">
                    <div class="errors__box" v-if="v$.userInputs.email.$error">
                        <p class="errors" v-for="(error, i) in v$.userInputs.email.$errors" :key="i">{{ error.$message }}</p>
                    </div>
                </div>
                <div class="formInputs">
                    <label for="emailConfirm">Confirm Email Address</label>
                    <input type="email" name="email" :style="[pendingFetch ? {opacity: 0.5} : {opacity:1}]" :disabled="pendingFetch" id="emailConfirm" v-model="userInputs.email_confirmation" placeholder="Re-type email address">
                    <div class="errors__box" v-if="v$.userInputs.email_confirmation.$error">
                        <p class="errors" v-for="(error, i) in v$.userInputs.email_confirmation.$errors" :key="i">{{ error.$message }}</p>
                    </div>
                    <div class="errors__box" v-if="emailChangeResponse.message">
                        <p class="errors" v-html="emailChangeResponse.message">&nbsp;</p>
                    </div>
                </div>
                <div class="formInputs" v-if="!pendingFetch">
                    <p class="current__email">{{ `Current Email: ${user.email}` }}</p>
                </div>
                <div v-if="pendingFetch" class="loader">
                    <svg viewBox="0 0 676 676">
                        <circle cx="338" cy="338" r="308"/>
                    </svg>
                </div>
                <div class="formInputs">
                    <button type="submit" :disabled="pendingFetch" :style="[pendingFetch ? {opacity: 0.5} : {opacity:1}]" role="button">Confirm &amp; Save</button>
                </div>
                <p v-if="SecuritySettingsErrors.length > 0 || SecuritySettingsErrors" :class="(SecuritySettingsErrors.code === 200) ? 'success' : 'errors'" v-html="SecuritySettingsErrors.message">&nbsp;</p>
            </form>

            <!-- A form to submit and request password change link. It contains only one "submit" button -->
            <form action="/" method="POST" @submit.prevent="resetPassword" enctype="multipart/form-data">
                <div class="formInputs">
                    <button type="submit" style="max-width:250px" :disabled="pendingFetch" :style="[pendingFetch ? {opacity: 0.5} : {opacity: 1}]" role="button">Request Reset Password</button>
                    <div class="response__box" v-if="passwordRequestResponse.length > 0">
                        <p class="response" v-html="passwordRequestResponse">&nbsp;</p>
                    </div>
                </div>
            </form>

            <form action="/" method="POST" @submit.prevent="activateTwoFa" enctype="multipart/form-data" v-if="!user.is_twofa">
                <!-- Form to enable Two Factor Authentication -->
                <div class="formInputs">
                    <button type="submit" style="font-size:14px" :disabled="pendingFetch" :style="[pendingFetch ? {opacity: 0.5} : {opacity: 1}]" role="button">
                        Activate Two Factor Authentication
                    </button>
                </div>
            </form>
            
            <form action="/" method="POST" @submit.prevent="deactivateTwoFa" enctype="multipart/form-data" v-if="user.is_twofa">
                <!-- Form to disable Two Factor Authentication -->
                <div class="formInputs">
                    <button type="submit" style="font-size:14px" :disabled="pendingFetch" :style="[pendingFetch ? {opacity: 0.5} : {opacity: 1}]" role="button">
                        Disable Two Factor Authentication
                    </button>
                </div>
            </form>

            <form action="/" method="GET" @submit.prevent="displayQrCode" enctype="multipart/form-data" v-if="user.is_twofa && (qrCodeSvg === '' || qrCodeSvg === null)">
                <!-- Form to display QR Code if 2-FA is enabled -->
                <div class="formInputs">
                    <button type="submit" style="font-size:14px" :disabled="pendingFetch" :style="[pendingFetch ? {opacity: 0.5} : {opacity: 1}]" role="button">
                        View QR Code
                    </button>
                </div>
            </form>

            <form action="/" method="GET" @submit.prevent="viewRecoveryCodes" enctype="multipart/form-data" v-if="user.is_twofa && (twoFaRecoveryCodes === '' || twoFaRecoveryCodes === null)">
                <!-- Form to display 2-FA recovery codes -->
                <div class="formInputs">
                    <button type="submit" style="font-size:14px" :disabled="pendingFetch" :style="[pendingFetch ? {opacity: 0.5} : {opacity: 1}]" role="button">
                        View Recovery Codes
                    </button>
                </div>
            </form>

            <!-- A form to delete account. It contains one submit button. -->
            <form action="/" method="POST" enctype="multipart/form-data">
                <div class="formInputs">
                    <button type="button" @click="$store.dispatch('toggleDeletionAlert', true)" style="max-width:250px" :disabled="pendingFetch" :style="[pendingFetch ? {opacity: 0.5} : {opacity: 1}]" role="button">Delete Account</button>
                </div>
            </form>

            <!-- Section to display Two Factor Authentication QR Code -->
            <div class="qrCode__container" v-if="user.is_twofa && qrCodeSvg">
                <p class="qrCode__descriptions">Please scan below QR Code on an authenticator app like <strong>Google Authenticator</strong></p>
                <div class="qrCode__view" v-html="qrCodeSvg">&nbsp;</div>
            </div>

            <!-- Section to display 2-factor authentication recovery codes -->
            <div class="twoFa__recovery__codes" v-if="twoFaRecoveryCodes">
                <p class="twoFa__recovery__codes__desc">Please copy and store your recovery codes somewhere safe.</p>
                <ul>
                    <li v-for="(code, i) in twoFaRecoveryCodes.recoveries" :key="i">{{code}}</li>
                </ul>
            </div>
        </div>
        <account-delete-warning v-if="deletionAlert"></account-delete-warning>
        <account-save-popup v-if="securitySettingsSaveReady" :path="submissionPath"></account-save-popup>
    </div>
</template>
<script>
    import { mapGetters } from 'vuex'
    import useVuelidate from '@vuelidate/core'
    import { required, email, minLength, maxLength, sameAs, helpers, requiredUnless } from '@vuelidate/validators'
    import AccountDeleteWarning from './tinyComponents/Account-Delete-Warning.vue'
    import AccountSavePopup from './tinyComponents/Account-Save-Popup.vue'

    export default {
        name: 'SecuritySettings',
        data () {
            return {
                submissionPath: ''
            }
        },
        setup () {
            return { v$: useVuelidate() }
        },
        components: {
            AccountDeleteWarning,
            AccountSavePopup
        },
        computed: {
            ...mapGetters([
                'user',
                'userInputs',
                'deletionAlert',
                'qrCodeSvg',
                'twoFaRecoveryCodes',
                'pendingFetch',
                'SecurityPasswordConfirm',
                'emailChangeResponse',
                'passwordRequestResponse',
                'securitySettingsSaveReady',
                'SecuritySettingsErrors',
            ]),
        },
        validations () {
            return {
                userInputs: {
                    email: { 
                        required: helpers.withMessage('To change your email you must enter a new, valid and unique email address.', required), 
                        email: helpers.withMessage('You entered an invalid email address. Please check entered email and re-try.', email), 
                        minLength: helpers.withMessage('Email address length must be at least 12 characters.', minLength(12)), 
                        maxLength: helpers.withMessage('You email address must be maximum 64 characters.', maxLength(64)),
                    },
                    email_confirmation: { 
                        required: helpers.withMessage('Email confirmation field is required. Please fill this field.', required), 
                        email: helpers.withMessage('Email confirmation is invalid. Please re-type same email address you entered above.', email), 
                        minLength: helpers.withMessage('Minimum length of email confirmation is 12 characters.', minLength(12)),
                        maxLength: helpers.withMessage('Maximum length of email confirmation is 64 characters.', maxLength(64)), 
                        sameAs: helpers.withMessage('Email confirmation is not same as email address field.', sameAs(this.userInputs.email)) 
                    },
                }
            }
        },
        methods: {
            async fetchUser () {
                await this.$store.dispatch('fetchUser')
            },
            saveEmail () { 
                this.v$.$validate()
                if (!this.v$.$error && !this.pendingFetch) {
                    this.submissionPath = 'email'
                    this.$store.dispatch('securitySettingsSaveReady', true)
                }
            },
            async resetPassword () {
                await this.$store.dispatch('passwordResetRequest')
            },
            async activateTwoFa () {
                await this.$store.dispatch('enableTwoFa')
                this.submissionPath = 'activateTwoFa'
            },
            async deactivateTwoFa () {
                await this.$store.dispatch('disableTwoFa')
                this.submissionPath = 'deactivateTwoFa'
            },
            async displayQrCode () {
                await this.$store.dispatch('twoFaQrCode')
                this.submissionPath = 'qrCode'
            },
            async viewRecoveryCodes () {
                await this.$store.dispatch('viewRecoveryCodes')
                this.submissionPath = 'recoverCodes'
            }
        },
        mounted () {
            this.fetchUser()
        }
    }
</script>