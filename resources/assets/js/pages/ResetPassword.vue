<template>
    <div class="enter-container">
        <div class="enter-main" style="width:52%">
            <div class="enter-block">
                <div class="enter-block__body" v-if="verifyToken">
                    <h3 class="enter-title" style="margin-top:3rem">Reset password</h3>
                    <form @submit="changePasswordWhenForgot">
                        <div>
                            <label for="password">New password</label>
                            <input v-model="form.new_password" type="password" name="password" id="password" class="form-control input-black" required />
                        </div>
                        <div style="margin-top:1rem">
                            <label for="password">Confirm password</label>
                            <input v-model="form.confirmPass" type="password" name="confirmPass" id="confirmPass" class="form-control input-black" required />
                        </div>
                        <hr>
                        <div class="flex flex-center">
                            <button
                                    type="submit"
                                    class="btn btn-primary"
                                    v-bind:disabled="(form.new_password == '' || form.confirmPass == '') || form.new_password != form.confirmPass"
                                    style="border-radius:20px;font-size:20px">
                                Save password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="section-border"></div>
        <img class="how-toBg" src="assets/img/how-to-action_BG.png" alt="bg-img" />
    </div>
</template>

<script>
export default {
    data() {
        return  {
            form: {
                new_password: '',
                confirmPass: ''
            },
            verifyToken: false, // For verify hash.
            isPasswordConfirm: false // Label that checks password confirm
        }
    },
    created() {
        this.verifyHash()
    },
    methods: {
        verifyHash() {
            this.$store.dispatch('users/verifyHash', this.$route.params.hash)
                .then((res) => {
                    if(true === res.success) {
                        this.verifyToken = true // Set verify status - true
                    }
                })
        },
        changePasswordWhenForgot(e) {
            e.preventDefault();

            if(this.confirmPassword()) {
                this.$store.dispatch('users/changePasswordWhenForgot', {token: this.$route.params.hash, password: this.form.new_password})
                    .then((res) => {
                        this.$router.push('/')
                    })
            }
        },
        confirmPassword() {
            if(this.form.new_password === this.form.confirmPass) {
                this.isPasswordConfirm = true
            } else {
                this.isPasswordConfirm = false
            }

            return this.isPasswordConfirm
        }
    }
}
</script>
