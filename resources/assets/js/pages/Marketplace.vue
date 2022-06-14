<template>
    <div>
        <div class="container">
            <div class="marketplace-title">
                <h3>Marketplace</h3>
            </div>
            <div class="sort-container__profile">
                <!-- <div class="main-sort__block marketplace">
                    <div class="type-packs__block type-packs__block-marketplace">
                        <select class="form-select sort-btn" aria-label="sort">
                            <option selected>Recently added</option>
                            <option value="1">Recieved (oldest)</option>
                            <option value="2">Recieved (newest)</option>
                            <option value="3">Highest Serial Number</option>
                            <option value="4">Lowest Serial Number</option>
                            <option value="5">Lowest ask (assending)</option>
                            <option value="6">Lowest ask (desending)</option>
                        </select>
                        <div class="tariff-plan__filter-block">
                            <button class="tariff-plan__btn active">All tires</button>
                        </div>
                    </div>
                    <div class="sort-types">
                        <p class="show-filter sort-btn marketplace">Show filter</p>
                    </div>
                </div> -->
                <div class="additional-sort additional-sort__marketplace">
                    <select class="form-select sort-btn" aria-label="sort">
                        <option selected>Artist</option>
                        <option value="1">Recieved (oldest)</option>
                        <option value="2">Recieved (newest)</option>
                        <option value="3">Highest Serial Number</option>
                        <option value="4">Lowest Serial Number</option>
                        <option value="5">Lowest ask (assending)</option>
                        <option value="6">Lowest ask (desending)</option>
                    </select>
                    <select class="form-select sort-btn" aria-label="sort">
                        <option selected>Genre</option>
                        <option value="1">Recieved (oldest)</option>
                        <option value="2">Recieved (newest)</option>
                        <option value="3">Highest Serial Number</option>
                        <option value="4">Lowest Serial Number</option>
                        <option value="5">Lowest ask (assending)</option>
                        <option value="6">Lowest ask (desending)</option>
                    </select>
                    <select class="form-select sort-btn" aria-label="sort">
                        <option selected>Series</option>
                        <option value="1">Recieved (oldest)</option>
                        <option value="2">Recieved (newest)</option>
                        <option value="3">Highest Serial Number</option>
                        <option value="4">Lowest Serial Number</option>
                        <option value="5">Lowest ask (assending)</option>
                        <option value="6">Lowest ask (desending)</option>
                    </select>
                    <div class="range-block">
                        <p class="range-title">Price range</p>
                        <div class="rangewrapper">
                            <div class="sliderfill">
                                <input
                                    class="customrange"
                                    type="range"
                                    min="0"
                                    max="10000"
                                    value="2000"
                                />
                            </div>
                            <div class="sliderthumb"></div>
                            <p class="slidervalue">kr</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="music-card__container">
                <ul class="music-card__list marketplace" v-if="nfts.length">
                    <li class="music-card__item" v-for="(item, i) in nfts" :key="i" v-if="item.metadata">
                        <router-link :to="'/nft/' + item.id">
                            <div class="music-img" style="text-align:center">
                                <img :src="item.metadata.cover_image" width="197" onerror="this.hidden=true"/>
                            </div>
                            <div class="music-desc__block">
                                <div class="music-title">
                                    <p class="item-music__title">{{item.metadata.name}}</p>
                                </div>
                                <p class="item-music__tariff-plan" v-if="item.type">
                                  {{ item.type.attribute_value }}
                                  <span v-if="item.numbering">#1 / {{item.numbering.attribute_value}}</span>
                                  <span class="plan-icon">{{ item.type.attribute_value.charAt(0) }}</span>
                                </p>
                                <p class="sale-status">Price</p>
                                <p class="item-music__price"><span v-if="settings.currency_rate_dkk">{{ Number(parseFloat(item.price * settings.currency_rate_dkk)).toFixed(2) }}kr.</span></p>
                            </div>
                        </router-link>
                    </li>
                </ul>
                <div v-else style="margin:10rem 0;color:#fff">
                    <div v-if="nfts.length || loader" class="flex-center">
                        <img src="assets/img/loader.gif" width="100px" />
                    </div>
                    <p v-else>No NFT’s are up for sale at the moment</p>
                </div>
            </div>
        </div>
        <div class="section-border"></div>
    </div>
</template>

<script>
export default {
    name: 'Profile',
    data() {
        return {
            nfts: [],
            loader: true,
            user: {},
            settings: {}
        }
    },
    created() {
        this.getAllSettings()
        this.allNftsForSale()
    },
    filters: {
        formatPrice(price) {
            if(price && '00' == price.split(".")[1]) return price.split(".")[0]
            return price
        }
    },
    methods: {
        getAllSettings() {
            this.$store.dispatch('settings/allSettings')
                .then(settings => {
                    this.settings = settings
                })
        },
        allNftsForSale() {
            this.loader = true

            this.$store.dispatch('nfts/allNftsForSale')
                .then(nfts => {
                    this.nfts = nfts
                    this.loader = false
                }).catch(error => {
                    this.loader = false
                    console.log(error)
                })
        }
    }
}
</script>
