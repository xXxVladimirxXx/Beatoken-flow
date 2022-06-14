<template>
    <div>
        <div class="back-sub-nav">
            <div class="container">
                <div class="back-sub-nav__block back-sub-nav__item">
                    <router-link to="/my-collection" class="back-to-pack__link"><i class="icon-arrow-left"></i>My activity</router-link>
                    <p>Listing Confirmation</p>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="item-success__wrapper">
                <div class="open-container__title">
                    <div class="order-status">
                        <p>Order status</p>
                        <span>Success</span>
                    </div>
                    <h3>Your item has been placed for sale</h3>
                </div>

                <div class="listing-summary__block">
                    <p>Listing Summary</p>
                    <div class="credit-block">
                        <p>Card item details</p>
                        <button>View listing</button>
                    </div>
                </div>
                <div class="for-sale__desc-block" v-if="nft.metadata">
                    <div class="img-block">
                        <img
                            :src="nft.metadata.cover_image"
                            height="173"
                            alt="for-sale-img"
                        />
                    </div>
                    <div class="desc">
                        <div class="music-card__item">

                            <h2>{{nft.metadata.name}}</h2>
                            <div class="rare-set" v-if="nft.pack">
                                <p>{{ nft.pack.release.set.name }}</p>
                                <p class="released">{{ nft.pack.release.name }} (released <span>{{ true | currentDate }}</span>)</p>
                            </div>

                            <p class="item-music__tariff-plan" v-if="nft.type">
                                {{ nft.type.attribute_value }} <span class="plan-icon">{{ nft.type.attribute_value.charAt(0) }}</span>
                            </p>
                            <p class="tariff-plan"><br>Price: <span>$</span>{{ nft.price | formatPrice }} <span>/ {{ Number(parseFloat(nft.price * settings.currency_rate_dkk)).toFixed(2) }}kr.</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: 'nft-success-place-for-sale',
    data() {
        return  {
            nft: {},
            settings: {}
        }
    },
    filters: {
        getType(attributes) {
            return attributes.find(e => e.attribute_type == 'type').attribute_value
        },
        currentDate() {
            return moment().format('DD.MM.YYYY')
        },
        formatPrice(price) {
            if(price && '00' == price.split(".")[1]) return price.split(".")[0]
            return price
        }
    },
    created() {
        this.getAllSettings()
        this.showNft()
    },
    methods: {
        getAllSettings() {
            this.$store.dispatch('settings/allSettings')
                .then(settings => {
                    this.settings = settings
                })
        },
        showNft() {
            this.$store.dispatch('nfts/showNft', this.$route.params.nft_id)
                .then((nft) => {
                    if (!nft.price) this.$router.push('/my-collection')
                    this.nft = nft
                })
        }
    }
}
</script>
