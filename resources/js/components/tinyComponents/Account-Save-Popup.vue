<template>
    <div class="security__settings__save__alert">
        <div class="security__settings__save">
            <form action="/" method="post" @submit.prevent="confirmSave" enctype="multipart/form-data">
                <div class="password__confirm__inputs">
                    <h2 class="form__title">Confirm Password</h2>
                    <p class="form__descriptions">Please enter your current account password to proceed.</p>
                    <div class="form__buttons">
                        <input type="password" name="password" autofocus autocomplete="false" v-model="userInputs.password" placeholder="Account Password*">
                        <div>
                            <button type="button" role="button" @click="cancelSaving" class="btnConfirmSave">Cancel</button>
                            <button type="submit" role="button" class="btnConfirmSave">Confirm & Save</button>
                        </div>
                    </div>
                    <div class="response__box" v-if="SecuritySettingsErrors.length > 0 || SecuritySettingsErrors">
                        <p :class="(SecuritySettingsErrors.code === 200) ? 'response' : 'errror'" v-html="SecuritySettingsErrors.message">&nbsp;</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    import { mapGetters } from 'vuex'
    import useVuelidate from '@vuelidate/core'
    import { required, email, minLength, maxLength, sameAs, helpers } from '@vuelidate/validators'
    export default {
        name: "AccountSavePopup",
        props: {
            path: String,
            profileChangesInputs: Object
        },
        data () {
            return {
            }
        },
        setup () {
            return { v$: useVuelidate() }
        },
        validations () {
            switch (this.path) {
                case "email":
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
                            password: { 
                                required: helpers.withMessage('Password Confirmation field is required. Please enter your current account password to save changes.', required), 
                                minLength: helpers.withMessage('Your account password must be at least 8 characters.', minLength(8)),
                                maxLength: helpers.withMessage('Your account password must be maximum 64 characters.', maxLength(64)), 
                            }
                        },
                    }
                    break;
                case "twoFa":
                    return {
                        userInputs: {
                            password: { 
                                required: helpers.withMessage('Password Confirmation field is required. Please enter your current account password to save changes.', required), 
                                minLength: helpers.withMessage('Your account password must be at least 8 characters.', minLength(8)),
                                maxLength: helpers.withMessage('Your account password must be maximum 64 characters.', maxLength(64)), 
                            }
                        },
                    }
                    break;
                default:
                    break;
            }
        },
        computed: {
            ...mapGetters([
                'pendingFetch',
                'SecuritySettingsErrors',
                'SecurityPasswordConfirm',
                'userInputs',
                'emailChangeResponse',
                'securitySettingsSaveReady',
            ]),
        },
        methods: {
            async confirmSave () {
                this.v$.$validate()
                if (!this.v$.$error && !this.pendingFetch) {
                    switch (this.path) {
                        case "email":
                            await this.$store.dispatch('saveSecuritySettings', {'email': this.userInputs.email, 'email_confirmation': this.userInputs.email_confirmation, 'password': this.userInputs.password})
                            break;
                        case "activateTwoFa":
                            await this.$store.dispatch('twoFaPassConfirm', {'password': this.userInputs.password}).then(() => {
                                this.$store.dispatch('enableTwoFa')
                            })
                            break;
                        case "deactivateTwoFa":
                            await this.$store.dispatch('twoFaPassConfirm', {'password': this.userInputs.password}).then(() => {
                                this.$store.dispatch('disableTwoFa')
                            })
                            break;
                        case "qrCode":
                            await this.$store.dispatch('twoFaPassConfirm', {'password': this.userInputs.password}).then(() => {
                                this.$store.dispatch('twoFaQrCode')
                            })
                            break;
                        case "recoverCodes":
                            await this.$store.dispatch('twoFaPassConfirm', {'password': this.userInputs.password}).then(() => {
                                this.$store.dispatch('viewRecoveryCodes')
                            })
                            break;
                        case "profileChanges": 
                            await this.$store.dispatch('twoFaPassConfirm', {'password': this.userInputs.password}).then(() => {
                                this.$store.dispatch('updateProfiles', this.profileChangesInputs)
                            })
                            break;
                        default:
                            alert("Invalid Input!")
                            break;
                    }
                }
                this.userInputs.password = ''
                this.v$.$reset
            },
            cancelSaving () {
                this.$store.dispatch('securitySettingsSaveReady', false)
            }
        }
    }
</script>
