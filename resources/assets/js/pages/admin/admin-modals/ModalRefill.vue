<template>
    <!-- MODAL -->
    <!-- REFILL -->
    <div
        class="modal fade"
        id="refill"
        tabindex="-1"
        aria-labelledby="prizeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="prizeModalLabel">Refill balance</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <i class="icon-btn-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-modal__data">
                        <img :src="user.full_uri_avatar" alt="user-avatar" />
                        <p>{{ user.name }}</p>
                    </div>
                    <div class="reset-password__form">
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <money placeholder="Enter price" id="inputRefill" class="form-control input-black" v-model="amount" />
                        </div>

                        <button @click="refillBalance" class="submit-btn">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'modal-refill',
    props: {
        user: {
            type: Object,
            required: false
        }
    },
    data() {
        return {
            amount: '1.00',
        }
    },
    methods: {
        refillBalance() {
            this.$loader(true)
            this.$store.dispatch('users/refillBalance', {user: this.user, amount: parseFloat(this.amount)})
                .then((resp) => {
                    this.$store.dispatch('flow/checkBalance', this.user.current_flow_account.address)
                        .then((resp) => {
                            const user = Object.assign({}, this.user)
                            user.balance = resp.encodedData.value

                            this.$store.commit('users/setUser', user)

                            this.$notify({title: 'The balance will be replenished by '+ parseFloat(this.amount) +' tokens within 15-30 seconds', type: 'success'});
                            this.$loader(false)
                            $('#refill').modal('hide')
                            this.amount = 1.00
                        }).catch(() => this.$loader(false))
                }).catch(() => this.$loader(false))
        }
    }
}
</script>
