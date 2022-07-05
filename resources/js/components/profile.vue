<template>
    <div class="profile-container">
        <div class="profile-middlebox">
            <div class="updating-layer" v-if="updating">
                <strong>saving...</strong>
            </div>
            <perfect-scrollbar>
                <div class="profile-pic-box-holder">
                    <div class="profile-pic-box">
                        <div class="profile-pic-overlay-layer">
                            <span>Change Avatar</span>
                        </div>
                        <img :src="`/storage/locals/${profiles.profile_picture}`" :alt="`${profiles.first_name} ${profiles.surname} profile`">
                    </div>
                    <div class="profile-name-box">
                        <input type="text" pattern="[A-Za-z]{3,}" v-model="user_inputs.fname" :placeholder="profiles.first_name">
                        <div class="errors" v-if="v$.user_inputs.fname.$error">
                            <ul>
                                <li v-for="(error, i) in v$.user_inputs.fname.$errors" :key="i">{{error.$message}}</li>
                            </ul>
                        </div>
                        <input type="text" pattern="[A-Za-z]{3,}" v-model="user_inputs.sname" :placeholder="profiles.surname">
                        <div class="errors" v-if="v$.user_inputs.sname.$error">
                            <ul>
                                <li v-for="(error, i) in v$.user_inputs.sname.$errors" :key="i">{{error.$message}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="info-section-box sections-box">
                    <h2 class="section-title">Information</h2>
                    <div class="inputs-box">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" v-model="user_inputs.gender">
                            <option value="female" :selected="profiles.gender === 'female'">female</option>
                            <option value="male" :selected="profiles.gender === 'male'">male</option>
                        </select>
                        <div class="errors" v-if="v$.user_inputs.gender.$error">
                            <ul>
                                <li v-for="(error, i) in v$.user_inputs.gender.$errors" :key="i">{{error.$message}}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="inputs-box dob-box">
                        <label for="dob">Date of Birth</label>
                        <v-date-picker class="inline-block h-full" v-model="user_inputs.dob" :max-date="`12/31/${year}`" :is-dark="$store.getters.preferences.settings.dark_mode ? true : false">
                            <template v-slot="{ inputValue, togglePopover }">
                                <div style="width:100% !important" class="flex items-center">
                                    <button
                                        class="p-2 bg-blue-100 border border-blue-200 hover:bg-blue-200 text-blue-600 rounded-l focus:bg-blue-500 focus:text-white focus:border-blue-500 focus:outline-none"
                                        @click="togglePopover()"
                                    >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        class="w-4 h-4 fill-current"
                                    >
                                        <path
                                        d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z"
                                        ></path>
                                    </svg>
                                    </button>
                                    <input
                                        :value="inputValue"
                                        class="bg-white text-gray-700 w-full py-1 px-2 appearance-none border rounded-r focus:outline-none focus:border-blue-500"
                                        readonly
                                    />
                                </div>
                            </template>
                        </v-date-picker>
                        <div class="errors" v-if="v$.user_inputs.dob.$error">
                            <ul>
                                <li v-for="(error, i) in v$.user_inputs.dob.$errors" :key="i">{{error.$message}}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="inputs-box" style="flex-direction:column !important">
                        <label for="descriptions" style="text-align:left;width:100%;">Descriptions</label>
                        <div class="errors" v-if="v$.user_inputs.descriptions.$error">
                            <ul>
                                <li v-for="(error, i) in v$.user_inputs.descriptions.$errors" :key="i">{{error.$message}}</li>
                            </ul>
                        </div>
                        <textarea name="descriptions" v-model="user_inputs.descriptions" cols="30" rows="10" :placeholder="(profiles.bio && user_inputs.desc_clear === null) ? profiles.bio : 'Describe who you are!'"></textarea>
                    </div>
                </div>
            </perfect-scrollbar>
            <form action="/" method="post" @submit.prevent="applyChanges" enctype="multipart/form-data">
                <button :class="readyToSave ? 'buttonActive' : 'buttonInactive'" role="button" :type="(readyToSave) ? 'submit' : 'button'" :disabled="readyToSave ? false : true">Save</button>
                <button type="button" @click="closeProfile" role="button">Close</button>
            </form>
        </div>
        <account-save-popup v-if="securitySettingsSaveReady" :profileChangesInputs="user_inputs" :path="submissionPath"></account-save-popup>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import moment from 'moment'
    import useVuelidate from '@vuelidate/core'
    import { minLength, maxLength, helpers } from '@vuelidate/validators'
    import AccountSavePopup from './tinyComponents/Account-Save-Popup.vue'

    const UniqueFname = (param) => (value) => value !== param
    const UniqueSname = (param) => (value) => value !== param
    //const UniqueAvatar = (param) => (value) => value !== param
    const UniqueDescriptions = (param) => (value) => (param == null || param == '') ? true : value !== param
    const GenderCorrection = (value) => value === 'female' || value === 'male'
    const ValidBdate = (value) => {
        var given = moment(value, "YYYY-MM-DD");
        var current = moment().startOf('day');
        var total = Math.abs(moment.duration(given.diff(current)).asYears())
        return (total >= 14 && total <= 200) ? true : false
    }

    export default {
        name: 'Profile',
        data () {
            return {
                user_inputs: {
                    fname: '',
                    sname: '',
                    avatar: '',
                    descriptions: '',
                    gender: '',
                    dob: null,
                },
                year: null,
                submissionPath: ''
            }
        },
        setup () {
            return { v$: useVuelidate() }
        },
        components: {
            AccountSavePopup,
        },
        computed: {
            ...mapGetters([
                'profiles',
                'updating',
                'readyToSave',
                'securitySettingsSaveReady'
            ]),
            maxDate () {
                var given = moment(this.profiles.birthdate, "YYYY-MM-DD");
                var current = moment().startOf('day');

                return Math.abs(moment.duration(given.diff(current)).asYears())
            },
        },
        validations () {
            return {
                user_inputs: {
                    fname: {
                        minLength: helpers.withMessage('Minimum length of first name input must be 3 characters.', minLength(3)), 
                        maxLength: helpers.withMessage('Your first name must be maximum 64 characters long.', maxLength(64)),
                        UniqueFname: helpers.withMessage('This is your current first name! Please pick different first name.', UniqueFname(this.profiles.first_name))
                    },
                    sname: {
                        minLength: helpers.withMessage('Your surname must be at least 3 characters long.', minLength(3)),
                        maxLength: helpers.withMessage('Maximum length of surname is 64 characters.', maxLength(64)), 
                        UniqueSname: helpers.withMessage('This is your current surname! Please pick different surname.', UniqueSname(this.profiles.surname))
                    },
                    descriptions: {
                        minLength: helpers.withMessage('Your bio must be at least 4 characters.', minLength(4)), 
                        maxLength: helpers.withMessage('Maximum length of bio must be 500 characters.', maxLength(500)),
                        UniqueDescriptions: helpers.withMessage('Please write different description. You cannot have same description.', UniqueDescriptions(this.profiles.bio))
                    },
                    gender: {
                        minLength: helpers.withMessage('Minimum length of gender input is 4 characters.', minLength(4)),
                        maxLength: helpers.withMessage('Maximum length of gender input is 6 characters.', maxLength(6)), 
                        GenderCorrection: helpers.withMessage('Invalid gender is selected.', GenderCorrection)
                    },
                    dob: {
                        ValidBdate: helpers.withMessage('Minimum age is 14 and maximum age is 200 years old!', ValidBdate)
                    }
                }
            }
        },
        methods: {
            resetForm () {
                this.user_inputs.fname = ''
                this.user_inputs.sname = ''
                this.user_inputs.avatar = ''
                this.user_inputs.descriptions = ''
                this.user_inputs.desc_clear = null
            },
            applyChanges () {
                this.v$.$validate()
                if (!this.v$.$error) {
                    // this.$store.dispatch('updateProfiles', this.user_inputs)
                    // this.resetForm()
                    this.submissionPath = 'profileChanges'
                    this.$store.dispatch('securitySettingsSaveReady', true)
                } else {
                    this.$store.dispatch('updateErrors', {"code": 500, "title": 'Invalid Form Submission', "message": 'Failed to save changes.<br/>Form inputs are not filled or prepared properly.', "button": 'dismiss', 'popup': true});
                }
            },
            closeProfile () {
                this.$store.dispatch('toggleProfiles', false)
            },
            recapitalize (input) {
                input = input.toLowerCase()
                input = input.charAt(0).toUpperCase() + input.slice(1)
                return input
            },
            resetInputs (){
                this.year = moment(new Date())
                this.year = this.year.subtract(15, "years")
                this.year = this.year.format("YYYY")
                this.user_inputs.gender = this.profiles.gender
                this.user_inputs.privacy = this.profiles.privacy
            },
            watchValidateForm () {
                this.v$.$validate()
                if (!this.v$.$error) {
                    this.$store.dispatch('toggleReadyToSave', true)
                } else {
                    this.$store.dispatch('toggleReadyToSave', false)
                }
            },
        },
        watch: {
            'user_inputs': {
                handler: 'watchValidateForm',
                deep: true
            }
        },
        mounted () {
            const parentHeight = document.querySelector('.profile-container').offsetHeight / 1.5
            this.$store.state.generals.leftActivePanels = false
            this.$store.state.generals.preferencesShow = false
            this.$store.state.generals.profilePreview = false
            this.$store.state.generals.rightActivePanels = false

            document.querySelector('.profile-middlebox').style.maxHeight = `${parentHeight}px`
            document.querySelector('.profile-middlebox').style.height = `${parentHeight}px`

            this.user_inputs.dob = this.profiles.birthdate
            this.resetInputs()
        }
    }
</script>
