<template>
    <div>
        <div class="collection-wrapper">
            <div class="container">
                <div class="collection-container">
                    <aside>
                        <div class="aside-user">
                            <div class="user-avatar__block">
                                <img :src="user.full_uri_avatar" alt="user-avatar" />
                            </div>
                            <div class="user-data__block">
                                <div class="user-edit__container">
                                    <div class="user-info">
                                        <p class="user-name">{{ user.name }}</p>
                                    </div>
                                    <div class="user-edit__btn-block">
                                        <router-link to="/edit-profile" class="edit-btn">Edit profile</router-link>
                                        <template v-if="user.current_flow_account != null && user.role.name != 'user' && user.role.name != 'middleman'">
                                            <a class="edit-btn" data-bs-toggle="modal" data-bs-target="#create-nft">Create NFT</a>
                                            <a class="edit-btn"
                                                v-bind:class="{active: modSale}"
                                                v-if="!whetherOnSale"
                                                @click="changeModSale">Create drop</a>
                                        </template>
                                    </div>
                                    <div class="user-share__block">
                                        <a :href="user.twitter_url" target="_blank"> <i class="icon-twitter"></i></a>
                                        <a :href="user.instagram_url" target="_blank"> <i class="icon-instagram"></i></a>
                                        <a :href="user.facebook_url" target="_blank"><i class="icon-facebook-circled"></i></a>
                                        <a :href="user.facebook_url" target="_blank"><i class="icon-share"></i></a>
                                    </div>
                                </div>
                                <p class="member-date">
                                    Member since
                                    <span>{{ user.created_at | formatDate }}</span>
                                </p>
                            </div>
                        </div>
                    </aside>

                    <main class="main-profile">
                        <div class="sort-container__profile">
                            <div class="main-sort__block">
                                <div class="type-packs__block">
                                    <button @click="whetherOnSale = false; inDrop = false;" class="btn type-change" v-bind:class="{active: !whetherOnSale}">Collectibles</button>
                                    <button @click="whetherOnSale = true; modSale = false; inDrop = false;" class="btn type-change" v-bind:class="{active: whetherOnSale && !inDrop}">For sale</button>
                                    <button @click="whetherOnSale = true; modSale = false; inDrop = true;" class="btn type-change" v-bind:class="{active: whetherOnSale && inDrop}">In Drop</button>
                                </div>
                            </div>
                        </div>
                        <div class="music-card__container" v-if="nfts.length">
                            <ul class="music-card__list" v-bind:class="{'active-mod-sale': modSale}">
                                <li class="music-card__item for-sale" v-for="(item, i) in nfts" :key="i" v-if="item.metadata != null && ((!whetherOnSale && !item.price && !item.drops.length) || (whetherOnSale && !inDrop && item.price && !item.drops.length) || (inDrop && item.drops.length))">
                                    <div class="music-desc__block">
                                        <router-link :to="'/nft/' + item.id">
                                            <span class="for-sale__mark" v-if="item.price">For sale</span>
                                            <div class="music-img" style="text-align:center">
                                                <img :src="item.metadata.cover_image" width="197" onerror="this.hidden=true"/>
                                            </div>
                                            <div class="music-title">
                                                <p class="item-music__title">{{item.metadata.name}}</p>
                                            </div>
                                            <p class="item-music__tariff-plan" v-if="item.type">
                                                {{ item.type.attribute_value }}
                                              <span v-if="item.numbering">#1 / {{item.numbering.attribute_value}}</span>
                                              <span class="plan-icon">{{ item.type.attribute_value.charAt(0) }}</span>
                                            </p>
                                            <template v-if="item.price">
                                                <p class="sale-status">Price</p>
                                                <p class="item-music__price" v-if="settings.currency_rate_dkk">{{ Number(parseFloat(item.price * settings.currency_rate_dkk)).toFixed(2) }}kr.</p>
                                            </template>
                                        </router-link>
                                    </div>
                                    <div class="flex flex-center" style="margin-top:0.5rem" v-if="modSale">
                                        <button class="btn btn-sm btn-success" v-if="!drop.idNftsForDrop.includes(item.id)" @click="drop.idNftsForDrop.push(item.id)">Select to drop</button>
                                        <button class="btn btn-sm btn-danger" v-else @click="drop.idNftsForDrop.splice(drop.idNftsForDrop.indexOf(item.id),1)">X</button>
                                    </div>
                                </li>
                            </ul>
                            <div class="flex flex-center" v-if="modSale">
                                <button v-bind:disabled="!drop.idNftsForDrop.length" style="margin-bottom:4rem" data-bs-toggle="modal" data-bs-target="#createDrop" class="btn btn-success refill-btn">
                                    CREATE DROP
                                </button>
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="nfts.length || loader" class="flex-center">
                                <img src="assets/img/loader.gif" width="100px" />
                            </div>
                            <div v-else>
                                <div class="empty-collection">
                                    <p>No cards to show yet - But you're nearly there!</p>
                                    <p class="mb">Buy a pack or grab a card at the marketplace</p>
                                    <router-link to="/marketplace">Marketplace</router-link>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="section-border"></div>
        </div>

        <!-- MODAL -->
        <!-- CreateDrop -->
        <div
            class="modal fade"
            id="createDrop"
            tabindex="-1"
            aria-labelledby="prizeModalLabel"
            aria-hidden="true"
            v-if="modSale"
            >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="prizeModalLabel">Create Pack</h5>
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
                        <div class="create-pack__mod" style="width:100%">
                            <div class="packs-title" style="width:100%">
                                <div class="flex-center">
                                    <div class="cover-block__img-nft">
                                            <div class="cover-img__nft flex-center" style="color:white" v-if="drop.cover_image != {}">
                                                {{ drop.cover_image.name }}
                                            </div>
                                            <label for="drop_cover" class="upload-btn">Select drop cover</label>
                                            <!-- <label>{{drop.cover_image.name}}</label> -->
                                            <input style="display:none" type="file" @change="processCoverForDropCreate($event)" id="drop_cover" accept="image/*">
                                    </div>
                                </div>

                                <div class="" style="color:#fff">

                                    <div class="create-nft__block">
                                        <div class="inputs-nft__block">
                                            <label>* Name</label>
                                            <input type="text" name="name" class="" v-model="drop.name" required>
                                        </div>

                                        <div class="inputs-nft__block">
                                            <label>* Release</label>
                                            <input type="text" name="release_name" class="" v-model="drop.release_name" required>
                                        </div>
                                    </div>


                                    <div class="create-nft__block">
                                        <div class="inputs-nft__block">
                                            <label>* Release start <small>(GMT {{utcDiff}})</small></label>
                                            <input type="datetime-local" name="release_start" class="" v-model="drop.release_start">
                                        </div>

                                        <div class="inputs-nft__block">
                                            <label>* Release end <small>(GMT {{utcDiff}})</small></label>
                                            <input type="datetime-local" name="release_end" class="" v-model="drop.release_end">
                                        </div>
                                    </div>


                                    <div class="create-nft__block">
                                        <div class="inputs-nft__block">
                                            <label>* Price</label>
                                            <money placeholder="Enter price" v-model="drop.price" />

                                            <div class="flex-center" style="display: none">
                                                <span v-if="settings.currency_rate_dkk">({{ Number(parseFloat(drop.price * settings.currency_rate_dkk)).toFixed(2) }}kr.)</span>
                                            </div>
                                        </div>

                                        <div class="inputs-nft__block">
                                            <label>* Short description</label>
                                            <input type="text" name="short_description" class="" v-model="drop.short_description" required>
                                        </div>
                                    </div>


                                    <div class="create-nft__block">
                                        <div class="inputs-nft__block textarea">
                                            <label>* Full description</label>
                                            <editor api-key="opijpx5o6t6quvytqu19j23b2utwobnpm23j0whxstbztpm3" :init="tinymce" v-model="drop.full_description"></editor>
                                        </div>
                                    </div>

                                    <div class="create-nft__block">
                                        <div class="inputs-nft__block">
                                            <label>Video url</label>
                                            <input type="text" name="video_url" class="" v-model="drop.video_url">
                                        </div>

                                        <div class="inputs-nft__block">
                                            <label>* Number nfts</label>
                                            <input type="number" name="number_nfts" step="1" min="1" max="100" class="f" v-model="drop.number_nfts">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 flex-center">
                                            <button @click="createDrop" v-bind:disabled="
                                                !drop.cover_image.name || drop.name == '' || drop.release_name == '' ||
                                                drop.release_start == '' || drop.release_end == '' || drop.price == '' ||
                                                drop.short_description == '' || drop.full_description == '' || drop.number_nfts == ''"
                                                class="upload-btn" style="margin-bottom: 20px">Create pack</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <create-nft @setNfts="setNfts" />

    </div>
</template>

<script>
import * as fcl from "@onflow/fcl";
import moment from "moment";
import Editor from '@tinymce/tinymce-vue';
import CreateNft from './components/modals/CreateNft';

export default {
    name: 'my-collection',
    components: {Editor, CreateNft},
    data() {
        return {
            drop: {
                name: '',
                release_name: '',
                release_start: '',
                release_end: '',
                price: '1,00',
                cover_image: {},
                short_description: '',
                full_description: '',
                video_url: '',
                number_nfts: '',
                status: 'draft',
                utc_user: '',
                idNftsForDrop: []
            },

            nfts: [],

            whetherOnSale: false,
            modSale: false,
            inDrop: false,
            loader: true,
            tinymce:  {
                plugins: [
                    "code fullscreen",
                ],
                relative_urls: false,
                min_height: 350,
                convert_urls: false,
            },
            settings: {},
            utcDiff: ''
        }
    },
    created() {
        this.parseUtcInfo()
        this.getAllSettings()
        this.$store.dispatch('users/getCurrentUser')
            .then((user) => {
                fcl.currentUser().subscribe((flowUser) => {
                    if (flowUser.loggedIn) {
                        this.$store.commit('flow/setFlowUser', flowUser)

                        this.allNfts()
                    } else {
                        fcl.authenticate()
                    }
                })
            })
    },
    filters: {
        formatDate(date) {
            return moment(date).format('MMM DD, YYYY')
        },
        formatPrice(price) {
            if(price && '00' == price.split(".")[1]) return price.split(".")[0]
            return price
        }
    },
    computed: {
        user() {
            return this.$store.getters['users/getCurrentUser']
        },
        flowUser() {
            return this.$store.getters['flow/getFlowUser']
        }
    },
    methods: {
        changeModSale() { this.modSale = !this.modSale },
        processCoverForDropCreate(e) { this.drop.cover_image = e.target.files[0] },
        parseUtcInfo() {
            var utcDiff = moment().parseZone().utcOffset()
            this.drop.utc_user = utcDiff

            let hours = parseInt(Math.abs(utcDiff) / 60, 10)
            let minutes = parseInt(Math.abs(utcDiff) % 60, 10);

            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;

            if (utcDiff > 0) { this.utcDiff = `${hours}:${minutes}`; } else { this.utcDiff = `-${hours}:${minutes}`; }
        },
        getAllSettings() {
            this.$store.dispatch('settings/allSettings')
                .then(settings => {
                    this.settings = settings
                })
        },
        setNfts(nfts) {
            this.nfts = nfts
        },
        allNfts() {
            this.loader = true
            this.nfts = []
            this.$store.dispatch('nfts/allNfts')
                .then(nfts => {
                    this.nfts = nfts
                    this.loader = false
                }).catch(error => {
                    console.log("NO NFT")
                    console.log(error)
                    this.loader = false
                })
        },
        createDrop() {
            this.$loader(true)

            if (this.settings.percent_drop_seller) {
                var priceForNftInFlow = (this.drop.price - (this.drop.price / 100 * this.settings.percent_drop_seller)) / this.drop.number_nfts; // count the commission
                var priceForNftInFlow = Number(parseFloat(priceForNftInFlow)).toFixed(8)

                const formData = new FormData()
                if (this.drop.name) formData.append('name', this.drop.name)
                if (this.drop.release_name) formData.append('release_name', this.drop.release_name)
                if (this.drop.release_start) formData.append('release_start', this.drop.release_start)
                if (this.drop.release_end) formData.append('release_end', this.drop.release_end)
                if (this.drop.price) formData.append('price', this.drop.price)
                if (this.drop.cover_image) formData.append('cover_image', this.drop.cover_image)
                if (this.drop.short_description) formData.append('short_description', this.drop.short_description)
                if (this.drop.full_description) formData.append('full_description', this.drop.full_description)
                if (this.drop.video_url) formData.append('video_url', this.drop.video_url)
                if (this.drop.number_nfts) formData.append('number_nfts', this.drop.number_nfts)
                if (this.drop.status) formData.append('status', this.drop.status)
                if (this.drop.utc_user) formData.append('utc_user', this.drop.utc_user)
                if (this.drop.idNftsForDrop) formData.append('idNftsForDrop', JSON.stringify(this.drop.idNftsForDrop))
                if (priceForNftInFlow) formData.append('priceForNftInFlow', priceForNftInFlow)

                this.$store.dispatch('drops/storeDrop', formData)
                    .then((resp) => {
                        this.nfts = resp.nfts
                        this.$store.dispatch('flow/putNftsForSale', {price: priceForNftInFlow, ids: resp.flowIds})
                            .then(tx => {
                                fcl.tx(tx).subscribe((res) => {
                                    if (res.status >= 4 && res.errorMessage == '') {
                                        this.$store.dispatch('drops/update', {id: resp.drop.id, status: 'active'})
                                            .then(() => {
                                                this.$loader(false)
                                                this.$notify({title: 'Your drop has been successfully created', type: 'success'});
                                                this.modSale = false;
                                                $('#createDrop').modal('hide')
                                                this.drop = {name: '', release_name: '', release_start: '', release_end: '', price: '', cover_image: {name:''}, short_description: '', full_description: '', video_url: '', number_nfts: '', idNftsForDrop: []};
                                            })
                                    } else if (res.status >= 4) {
                                        console.log('error@res', res)
                                        this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                                        this.$store.dispatch('drops/delete', resp.drop.id)
                                        this.$loader(false)
                                    }
                                })
                            }).catch((e) => {this.$loader(false); this.$store.dispatch('drops/delete', resp.drop.id)})
                    }).catch((e) => {this.$loader(false); this.$notify({title: 'Server error. Please try again later', type: 'error'}) })

            } else {
                this.$notify({ title: 'There must be a commission. Please try again later', type: 'error'});
                this.$loader(false)
            }
        }
    }
}
</script>
<style scoped>

hr {
    margin-top: 2rem
}
.music-card__list {
    transition: all 1s;
}
.user-edit__btn-block a.active {
    background-color: #198754
}
.active-mod-sale {
    border: 1px solid #3fbc6c;
    padding: .5rem;
    border-radius: 1rem;
}
.empty-collection {
    color: #fff;
    font-family: Shapiro\ 55 Middle;
    font-size: .875rem;
    margin-bottom: 400px;
    margin-top: 65px;
    text-align: center;
}
.empty-collection .mb {
    margin-bottom: 30px;
}
.empty-collection a {
    background-color: #1c70de;
    border-radius: 20px;
    color: #fff;
    font-family: Shapiro\ 75 Heavy;
    font-size: .875rem;
    padding: 7px 24px 10px;
    transition: all .3s;
}
</style>
