<template>
    <div>
        <div class="header-drop">
            <div class="how-to-action drop">
                <div class="how-toBg"></div>
                <h3 class="visually-hidden">Highlighted Drops</h3>
                <div class="container">
                    <h2 class="section-drop-title">Highlighted Drop</h2>
                    <div class="sets-block" v-if="currentDrop.nfts">
                        <div class="set" v-bind:class="{ 'set-one': currentDrop.nfts.length, 'set-two': !currentDrop.nfts.length }">
                            <div class="bg-txt"></div>
                            <div class="set-block">
                                <div class="set-one__txt">
                                    <span class="set-state" v-if="currentDrop.nfts && currentDrop.nfts.length">Now available</span>
                                    <span class="set-state" v-else>Sold out</span>

                                    <h3>
                                        {{ currentDrop.name }}
                                        <span>(Drop 1)</span>
                                    </h3>

                                    <p class="common">Common | Contains <span>{{currentDrop.number_nfts}}</span> cards</p>
                                </div>
                                <div class="set-one__img">
                                    <router-link :to="'/drop/' + currentDrop.id" >
                                        <img
                                            :src="currentDrop.full_uri"
                                            class="set-img"
                                            width="190"
                                            height="224"
                                            alt="set-img"
                                        />
                                    </router-link>
                                </div>
                            </div>
                            <p class="price">Price:
                                <span v-if="settings.currency_rate_dkk">
                                    {{ Number(parseFloat(currentDrop.totalSum * settings.currency_rate_dkk)).toFixed(2) }}
                                </span>
                                <span>kr <small>(with fee {{settings.percent_drop_buyer}}%)</small></span>
                            </p>
                        </div>

                        <div class="set set-two">
                            <div class="bg-txt"></div>

                            <div class="set-block">
                                <div class="set-two__txt">
                                    <span class="set-state">Sold out</span>
                                    <h3>
                                        Holo set
                                        <span>(Drop 2)</span>
                                    </h3>
                                    <p>Common gold</p>
                                    <p class="common">Contains <span>3</span> cards</p>
                                </div>
                                <div class="set-two__img">
                                    <router-link to="/drops">
                                        <img
                                            src="https://beatoken.com/wp-content/themes/beatoken/core/img/drop_holo_pack.png"
                                            class="set-img"
                                            width="110"
                                            height="121"
                                            alt="set-img"
                                        />
                                    </router-link>
                                </div>
                            </div>
                            <p class="price">Price: <span>25kr.</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="section-open-packs">
            <h3 class="visually-hidden">Open packs to find exciting new moments</h3>
            <div class="container">
                <h2 class="section-drop-title">
                    Open packs to find exciting new moments
                </h2>
                <div class="open-packs__container">
                    <div class="row">
                        <div class="open-packs__block item col-lg-4">
                            <h3>When do new drops happen?</h3>
                            <p>
                                During our Beta, drop times will vary, so make
                                sure you watch for announcements.
                            </p>
                        </div>
                        <div class="open-packs__block col-lg-4">
                            <h3>Where are drop announcements?</h3>
                            <p>
                                Sign up via email and recieve our newsletter to never miss a
                                drop
                            </p>
                        </div>
                        <div class="open-packs__block col-lg-4">
                            <h3>What about sold out packs?</h3>
                            <p>
                                You can still find collectibles from the packs in the
                                <a href="https://my.beatoken.com/marketplace">Marketplace</a> from other collectors
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section-packs">
            <h3 class="visually-hidden">Packs</h3>
            <div class="container">
                <div class="previous-packs">
                    <div class="packs-title">
                        <h3 class="section-drop-title">Previous packs</h3>
                        <p>
                            These packs have been flying off the shelves. But, don’t worry,
                            you can start hunting for specific Moments on the marketplace
                            now!
                        </p>
                    </div>
                    <ul class="packs-list previous-list">
                        <li class="pack-item">
                            <div class="img-pack">
                                <img
                                    src="https://beatoken.com/wp-content/themes/beatoken/core/img/drop_platinum_pack.png"
                                    width="197"
                                    height="192"
                                    alt="pack-img"
                                />
                            </div>

                            <h3 class="pack-title">B.O.C X COZY RUGZ</h3>
                            <span class="pack-set">DROP 1 PLATINUM PACK</span>
                            <p class="pack-price">350 <span>kr.</span></p>
                            <p class="pack-quantity sold">58 <span>sold out</span></p>
                        </li>
                        <li class="pack-item">
                            <div class="img-pack">
                                <img
                                    src="https://beatoken.com/wp-content/themes/beatoken/core/img/drop_holo_pack.png"
                                    width="197"
                                    height="192"
                                    alt="pack-img"
                                />
                            </div>

                            <h3 class="pack-title">BETA TEST DROP</h3>
                            <span class="pack-set">Drop 0</span>
                            <p class="pack-price">250 <span>kr.</span></p>
                            <p class="pack-quantity sold">10 <span>sold out</span></p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <div class="section-border"></div>
    </div>
</template>

<script>
export default {
    name: 'drops',
    data() {
        return {
            currentDrop: {},
            settings: {}
        }
    },
    created() {
        this.getAllSettings()
    },
    methods: {
        getAllSettings() {
            this.$store.dispatch('settings/allSettings')
                .then(settings => {
                    this.settings = settings
                    this.getCurrentDrop()
                })
        },
        getCurrentDrop() {
            this.$store.dispatch('drops/getCurrentDrop')
                .then((currentDrop) => {
                    var commissionForBuyDrop = currentDrop.price / 100 * (Number(this.settings.percent_drop_seller) + Number(this.settings.percent_drop_buyer)); // consider the commission
                    var totalSum = commissionForBuyDrop + (currentDrop.nfts[0].price * currentDrop.number_nfts)

                    this.currentDrop = currentDrop
                    this.currentDrop.totalSum = totalSum
                })
        }
    }
}
</script>
