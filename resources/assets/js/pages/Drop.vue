<template>
    <div>
        <div class="drop-main__container">
            <div class="container">
                <div class="drop-main__wrapper">
                    <div class="drop-main__header">
                        <div class="drop-header__img-block">
                            <img
                                :src="drop.full_uri"
                                width="409"
                                height="465"
                                alt="pack-img"
                            />
                        </div>
                        <div class="drop-main__aside">
                            <div class="aside-title__block">
                                <span class="set-state border-gradient-purple" v-if="drop.nfts && drop.nfts.length">Now on sale</span>
                                <span class="set-state border-gradient-purple" v-else>Sold out</span>
                                <h2 class="aside-pack__title">{{drop.name}} DROP 1</h2>
                                <p class="total-for-sale">
                                    <span v-if="drop.nfts">{{drop.nfts.length}}</span> total items for sale
                                </p>
                                <p class="pack-desc">
                                    {{drop.short_description}}
                                </p>
                            </div>
                            <div class="aside-count__block">
                                <div class="pack-sum">
                                    <p>1 pack with {{drop.number_nfts}} cards</p>
                                </div>

                                <p class="pack-price">
                                    <span v-if="settings.currency_rate_dkk">
                                    {{ Number(parseFloat(drop.totalSum * settings.currency_rate_dkk)).toFixed(2) }}
                                </span>
                                    <span>kr <small style="font-size:0.6em">(with fee {{settings.percent_drop_buyer}}%)</small></span>
                                </p>
                                <div class="join-button flex-center" v-if="drop.nfts && drop.nfts.length" @click="joinDrop">Join drop</div>
                            </div>
                            <div class="aside-logo__block">
                                <img src="assets/img/logo.png" width="42" height="42" alt="" />
                                <div class="logo-txt">
                                    <span>Sold by</span>
                                    <p>Beatoken</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="drop-details__block">
                        <p class="details-block__title">Pack details</p>
                        <div class="details-desc__block">
                            <p v-html="drop.full_description"></p>
                        </div>
                    </div>
                    <!-- <div class="drop-main__details"></div> -->
                </div>
            </div>
        </div>
        <div class="section-border"></div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: 'drop',
    data() {
        return {
            drop: {},
            settings: {}
        }
    },
    created() {
        this.getAllSettings()
    },
    filters: {
        currentDate() {
            return moment().format('DD.MM.YYYY')
        },
        formatPrice(price) {
            if(price && '00' == price.split(".")[1]) return price.split(".")[0]
            return price
        }
    },
    mounted() {
        this.sliderFor = this.$refs.sliderFor
        this.sliderNav = this.$refs.sliderNav
    },
    methods: {
        getAllSettings() {
            this.$store.dispatch('settings/allSettings')
                .then(settings => {
                    this.settings = settings
                    this.getDrop()
                })
        },
        getDrop() {
            this.$store.dispatch('drops/showDrop', this.$route.params.drop_id)
                .then((drop) => {
                    var commissionForBuyDrop = drop.price / 100 * (Number(this.settings.percent_drop_seller) + Number(this.settings.percent_drop_buyer)); // consider the commission
                    var totalSum = commissionForBuyDrop + (drop.nfts[0].price * drop.number_nfts)

                    this.drop = drop
                    this.drop.totalSum = totalSum
                }).catch(() => this.$router.push('/drops'))
        },
        joinDrop() {
            this.$store.dispatch('drop_lines/storeDropLineByDrop', this.drop.id)
                .then(() => {
                    this.$router.push('/drop-line/' + this.drop.id)
                })
        }
    }
}
</script>
