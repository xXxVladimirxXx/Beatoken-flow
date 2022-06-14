<template>
    <!-- MODAL Refill balance by card -->
    <div
        class="modal fade"
        id="refill-balance-by-card"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-dialog-contact">
            <div class="modal-content modal-content-contact">
                <div class="modal-header modal-header-contact">
                    <h5 class="modal-title">Refill balance</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <i class="icon-btn-close"></i>
                    </button>
                </div>
                <div class="modal-body modal-body-contact">
                    <div class="refill-block" style="display:block">
                        <p style="margin-bottom: 10px">Buy BLUcoins, pay with your Credit Card</p>
                        <div class="flex-column flex-center">
                            <div class="" style="display: flex; align-items: center; margin: 20px 0">
                                <span style="margin-right: 20px">BLUcoin</span>
                                <money placeholder="Enter amount" class="money" v-model="amount" />
                            </div>

                            <button class="refill" @click="refill">Refill</button>
                        </div>

                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'refill-balance-by-card',
    data() {
        return {
            amount: '1,00'
        }
    },
    methods: {
        refill() {
            this.$store.dispatch('payments/throughStripeGateway', {
                success_url: this.buyAfterPaymentThisUrl,
                amount: Number(parseFloat(this.amount)).toFixed(2)
            }).then((resp) => {
                window.location.href = resp.session.url
            }).catch((err) => {
                this.$notify({title: 'Something went wrong, please try again later', type: 'error'});
                console.log('err', err)
            })
        }
    }
}

</script>
