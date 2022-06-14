<template>
    <div>
        <main class="drop-full__screen-wrapper count">
            <div class="container">
                <div class="drop-count__container">
                    <div class="drop-count-wrapper">
                        <div class="drop-count__logo-block">
                            <img src="assets/img/logo.png" class="img-logo" alt="logo" />
                            <img
                                src="assets/img/beatokenTextLogo.svg"
                                class="text-logo"
                                alt="text-logo"
                            />
                        </div>
                        <div class="drop-count__title-block">
                            <h3>{{ drop.name }}</h3>
                            <p class="pack-sale" v-if="drop.nfts"><span>{{drop.nfts.length}}</span>total packs for sale</p>
                            <p class="pack-desc">
                                {{ drop.short_description }}
                            </p>
                        </div>
                        <div class="drop-count__count-block">
                            <img
                                :src="drop.full_uri"
                                alt="drop-img"
                            />

                            <div class="event-timer__block">
                                <div class="timer">
                                    {{ displayTime }}
                                </div>
                            </div>
                            <div class="col-12 flex flex-around">
                                <button @click="buyDrop(drop)" class="btn btn-primary" style="color: #fff" :disabled="!canBuy">Buy drop</button>
                                <button @click="buyByCard(drop)" class="btn btn-primary" style="color: #fff" :disabled="!canBuy">Buy drop with card</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <div class="section-border"></div>
    </div>
</template>

<script>
import * as fcl from "@onflow/fcl";

export default {
    name: 'drop-buy',
    data() {
        return {
            drop: {},
            settings: {},
            canBuy: false,
            timeToBuy: 0,
            timer: null,
            displayTime: '',
        }
    },
    beforeCreate() {
        this.$loader(true)
        this.$store.dispatch('drop_lines/myTimeToBuyDrop', this.$route.params.drop_id)
            .then((resp) => {
                if (resp.redirectToLine) {
                    this.$notify({title: 'The time that you had for payment has expired, you can line up again', type: 'error'})
                    this.$router.push('/drop-line/' + this.$route.params.drop_id)
                }
                if (resp.redirectToDrop) this.$router.push('/drop/' + this.$route.params.drop_id)

                this.dropLine = resp.dropLine
                this.timeToBuy = resp.timeToBuy
                this.startTimer()
                if (!this.$route.query.session_id) setTimeout(() => {this.$loader(false)},1000)
            })
    },
    created() {
        this.getAllSettings()
        this.getDrop()

        fcl.currentUser().subscribe((flowUser) => {
            if (flowUser.loggedIn) {
                this.$store.commit('flow/setFlowUser', flowUser)
            } else {
                this.$notify({title: 'You must be logged into your blocto account', type: 'error'})
                fcl.authenticate()
            }
        })
    },
    watch: {
        timeToBuy(time) {
            if (time === 0) {
                this.stopTimer()
            }
        }
    },
    computed: {
        flowUser() {
            return this.$store.getters['flow/getFlowUser']
        },
        user() {
            return this.$store.getters['users/getCurrentUser']
        }
    },
    methods: {
        startTimer() {
            this.canBuy = true
            this.timer = setInterval(() => {
                this.timeToBuy--

                let minutes = parseInt(this.timeToBuy / 60, 10)
                let seconds = parseInt(this.timeToBuy % 60, 10)

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                this.displayTime = `${minutes}min  ${seconds}sec`;
            }, 1000)
        },
        stopTimer() {
            clearTimeout(this.timer)
            this.canBuy = false
            this.$store.dispatch('drop_lines/deleteByDrop', this.$route.params.drop_id)
            this.$router.push('/drops')
        },
        getAllSettings() {
            this.$store.dispatch('settings/allSettings')
                .then(settings => {
                    this.settings = settings
                })
        },
        getDrop() {
            this.$store.dispatch('drops/showDrop', this.$route.params.drop_id)
                .then((drop) => {
                    this.drop = drop
                    if (!drop.nfts.length) this.$router.push('/drop/' + this.$route.params.drop_id)
                    if (this.$route.query.session_id) this.updatePaymentRefillBalance()
                }).catch(() => this.$router.push('/drops'))
        },
        updatePaymentRefillBalance() {
            this.$loader(true)
            this.$store.dispatch('payments/updatePaymentRefillBalance', {session_id: this.$route.query.session_id})
                .then((resp) => {
                    console.log('resp', resp)
                    this.$notify({text: 'Congratulations, your balance has been replenished', type: 'success'})
                    this.buyDrop(this.drop)
                }).catch((e) => {
                    console.log('e', e)
                    this.$notify({text: 'The session has already expired', type: 'warn'})
                    this.$loader(false)
                    this.$router.push('/')
                })
        },
        buyByCard(drop){
            var commissionForBuyDrop = drop.price / 100 * (Number(this.settings.percent_drop_seller) + Number(this.settings.percent_drop_buyer)); // consider the commission
            var totalSum = commissionForBuyDrop + (drop.nfts[0].price * drop.number_nfts)

            this.$store.dispatch('payments/throughStripeGateway', {
                buyAfterPaymentThisUrl: window.location.href,
                amount: Number(parseFloat(totalSum)).toFixed(8)
            }).then((resp) => {
                window.location.href = resp.session.url
            }).catch((err) => {
                this.$notify({title: 'Something went wrong, please try again later', type: 'error'});
                console.log('err', err)
            })
        },
        buyDrop(drop) {
            if (!this.canBuy || !this.flowUser) { return false }

            this.$store.dispatch('drops/showDrop', drop.id)
                .then((drop) => {

                    if (!drop.nfts.length) {
                        this.$notify({ title: 'All tokens in this drop have been sold', type: 'warn'});
                        this.$router.push('/drop/' + drop.id);
                        return false;
                    } // check if there are still tokens in the drop

                    this.$store.dispatch('users/getMiddlemanUser')
                        .then((user) => {
                            if (this.settings.percent_drop_seller && this.settings.percent_drop_buyer && user.current_flow_account) {

                                this.$loader(true)
                                this.$store.dispatch('flow/checkBalance', this.flowUser.addr) // get user balance
                                    .then(res => {

                                        var balanceUser = res.encodedData.value
                                        var commissionForBuyDrop = drop.price / 100 * (Number(this.settings.percent_drop_seller) + Number(this.settings.percent_drop_buyer)); // consider the commission

                                        var ids = []; var flowIds = [];
                                        while (drop.number_nfts > flowIds.length) {
                                            var nft = Object.values(drop.nfts)[Math.floor(Math.random() * drop.nfts.length)] // Get random nft
                                            if (!flowIds.find(x => x == nft.flow_id)) { ids.push(nft.id); flowIds.push(nft.flow_id); } // Check if there are duplicate id's and push
                                        }

                                        var totalSum = commissionForBuyDrop + (nft.price * ids.length)

                                        if (balanceUser < totalSum) {
                                            this.$notify({title: 'You do not have enough funds on your balance to buy this Drop. You can top up the balance in your profile', type: 'error'});
                                            this.$loader(false)
                                            return false;
                                        }

                                        this.$store.dispatch('flow/buyTokens', {
                                            middleman: user.current_flow_account.address,
                                            commission: commissionForBuyDrop,
                                            ownerNft: nft.flow_account.address,
                                            ids: flowIds,
                                            price: nft.price
                                        }).then(tx => {
                                            this.$notify({title: 'The nft purchase request has been accepted. Processed within a minute'});
                                            fcl.tx(tx).subscribe((res) => {
                                                if (res.status >= 4 && res.errorMessage == '') {
                                                    this.$store.dispatch('drops/buyDrop', {id: drop.id, ids, hash: tx.transactionId, commission: commissionForBuyDrop, price: nft.price})
                                                        .then(() => {
                                                            this.$notify({ title: 'Your collection has been updated', type: 'success'});
                                                            this.canBuy = false
                                                            this.$loader(false)
                                                            this.$router.push('/my-collection')
                                                        })
                                                } else if (res.status >= 4) {
                                                    console.log('error@res', res)
                                                    this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                                                    this.$loader(false)
                                                }
                                            })
                                        }).catch((e) => { this.$loader(false); console.log('e', e) })
                                    }).catch((e) => { console.log(e); this.$notify({title: 'Error with your balance. Go to your profile for more information', type: 'error'}); this.$loader(false) })
                            } else { this.$notify({ title: 'There must be a commission. Please try again later', type: 'error'}); }
                        })
                })
        }
    }
}
</script>
