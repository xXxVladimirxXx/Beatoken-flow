<template>
    <div class="container flex-center">
        <div style="text-align:center;color:white;margin:4.5rem 0">
            <h1>Thank you for your payment</h1>
            <h3>Your payment has been successfully completed</h3>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'success-payment',
        data() {
            return {}
        },
        created() {
            if (this.$route.query.session_id) {
                this.updatePaymentRefillBalance()
            } else { this.$router.push('/') }
        },
        methods: {
            updatePaymentRefillBalance() {
                this.$loader(true)
                this.$store.dispatch('payments/updatePaymentRefillBalance', {session_id: this.$route.query.session_id})
                    .then((resp) => {
                        console.log('resp', resp)
                        this.$notify({text: 'Congratulations, your balance has been replenished', type: 'success'})
                        this.$loader(false)
                        this.$router.push('/')
                    }).catch((e) => {
                        console.log('e', e)
                        this.$notify({text: 'The session has already expired', type: 'warn'})
                        this.$loader(false)
                        this.$router.push('/')
                    })
            }
        }
    }
</script>
