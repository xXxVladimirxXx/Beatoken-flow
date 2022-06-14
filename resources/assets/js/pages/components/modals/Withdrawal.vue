<template>
    <div
        class="modal fade"
        id="modal-withdrawal-balance"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-dialog-contact">
            <div class="modal-content modal-content-contact">
                <div class="modal-header modal-header-contact">
                    <h5 class="modal-title">Withdrawal</h5>
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
                        <p style="margin-bottom: 10px">Type amount BLUcoin you want to withdrawal</p>
                        <div class="flex-column flex-center">
                            <div style="display:flex;align-items:center;margin:20px 0">
                                <span style="margin-right: 20px">USD</span>
                                <money placeholder="Enter amount" class="money" v-model="amount" />
                            </div>

                            <button class="refill" @click="withdrawal">Send request</button>
                        </div>

                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
</template>

<script>
import * as fcl from "@onflow/fcl";

export default {
    name: 'withdrawal',
    data() {
        return {
            amount: '1,00'
        }
    },
    computed: {
        flowUser() {
            return this.$store.getters['flow/getFlowUser']
        }
    },
    methods: {
        withdrawal() {
            let amount = Number(parseFloat(this.amount)).toFixed(2)

            this.$store.dispatch('users/getWithdrawManagerUser')
                .then((user) => {
                    if (user.current_flow_account) {

                        this.$loader(true)
                        this.$store.dispatch('flow/checkBalance', this.flowUser.addr) // get user balance
                            .then(res => {
                                var balanceUser = res.encodedData.value

                                if (balanceUser < amount) {
                                    this.$notify({
                                        title: 'You do not have enough funds on your balance to buy this Drop. You can top up the balance in your profile',
                                        type: 'error'
                                    });
                                    this.$loader(false)
                                    return false;
                                }

                                this.$store.dispatch('flow/transferBlucoins', {amount: amount, address: user.current_flow_account.address})
                                    .then(tx => {

                                        fcl.tx(tx).subscribe((res) => {
                                            if (res.status >= 4 && res.errorMessage == '') {

                                                this.$store.dispatch('withdrawal_requests/create', {
                                                    amount: amount,
                                                    status: 'pending'
                                                }).then(() => {
                                                    this.$notify({title: 'Payment request was successfully sent', type: 'success'});

                                                    this.$store.dispatch('flow/checkBalance', this.flowUser.addr)
                                                        .then(res => {
                                                            this.flowUser.balance = res.encodedData.value
                                                            this.$store.commit('flow/setFlowUser', this.flowUser)
                                                        })

                                                    $('#modal-withdrawal-balance').modal('hide')
                                                    this.amount = '1,00'
                                                    this.$loader(false)
                                                })
                                            } else if (res.status >= 4) {
                                                console.log('error@res', res)
                                                this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                                                this.$loader(false)
                                            }
                                        })
                                    })
                            })
                    } else {
                        $('#modal-withdrawal-balance').modal('hide')
                        this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                        this.$loader(false)
                    }
                })

        }
    }
}
</script>
