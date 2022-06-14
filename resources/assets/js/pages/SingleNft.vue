<template>
  <div>
    <div class="container">
      <div class="pack-view__container">
        <div class="pack-carousel__block">
          <div class="carousel-pack">
            <VueSlickCarousel
                v-if="nft.metadata"
                ref="sliderFor"
                :asNavFor="sliderNav"
                :slidesToShow="1"
                :slidesToScroll="1"
                :arrows="false"
                :fade="true"
                :focusOnSelect="true"
                class="slider-for">

                <div class="pack1">
                  <img :src="nft.metadata.cover_image"/>
                </div>
                <div class="pack1" v-if="nft.extension_file != 'mp3'">
                  <img
                      v-if="nft.extension_file == 'jpeg' || nft.extension_file == 'jpg'"
                      :src="nft.metadata.source_file"
                  />
                  <video
                      v-if="nft.extension_file == 'mp4'"
                      :src="nft.metadata.source_file"
                      preload
                      autoplay
                      controls
                      width="100%"
                  />
                </div>
                <div class="pack1" v-if="nft.extension_file == 'mp3'">
                  <div class="flex flex-center">
                    <img :src="nft.metadata.cover_image">
                    <img src="/assets/img/audio.svg" class="audio-icon" style="position:absolute">
                  </div>
                </div>
                <div class="pack1" v-if="nft.congratulations_video">
                  <video
                      :src="nft.congratulations_video.attribute_value"
                      muted
                      autoplay
                      controls
                      loop
                      width="100%"
                      height="444px"
                  />
                </div>
            </VueSlickCarousel>

            <VueSlickCarousel
                v-if="nft.metadata"
                ref="sliderNav"
                :asNavFor="sliderFor"
                :slidesToShow="3"
                :slidesToScroll="1"
                :dots="false"
                :arrows="true"
                :centerMode="false"
                :focusOnSelect="true"
                class="slider-nav">

                <div class="pack2">
                  <img :src="nft.metadata.cover_image"/>
                </div>
                <div class="pack2" v-if="nft.extension_file != 'mp3'">
                  <img
                      v-if="nft.extension_file == 'jpeg' || nft.extension_file == 'jpg'"
                      :src="nft.metadata.source_file"
                  />

                  <video
                      v-if="nft.extension_file == 'mp4'"
                      :src="nft.metadata.source_file"
                      muted
                      preload=""
                      width="106"
                      height="106"
                  />
                  <img v-if="nft.extension_file == 'mp4'"
                       src="/assets/img/play.svg" class="video-icon-2">
                </div>
                <div class="pack2" v-if="nft.extension_file == 'mp3'" @click="setTrack()">
                    <img src="/assets/img/audio.svg" class="audio-icon-2">
                </div>
                <div class="pack2" v-if="nft.congratulations_video">
                  <video
                      :src="nft.congratulations_video.attribute_value"
                      muted
                      autoplay
                      loop
                      width="106"
                      height="106"
                  />
                </div>
            </VueSlickCarousel>
          </div>
        </div>
        <div class="pack-top__block">
          <div class="artist-desk">
            <p class="artist-name" v-if="nft.metadata">{{ nft.metadata.name }}</p>
          </div>
          <div class="tariff-plan__block" v-if="nft.type">
            <p class="tariff-plan tariff-plan__card-view">
              {{ nft.type.attribute_value }} <span class="plan-icon">{{ nft.type.attribute_value.charAt(0) }}</span>
            </p>
          </div>
        </div>

        <div class="pack-bottom__block" v-if="!isCurrentUserOwner">
          <div class="price-rate__block" v-if="nft.price">
            <div class="lowest-ask__block">
              <p class="price-title">Price</p>
              <p class="price"><span v-if="settings.currency_rate_dkk">{{ Number(parseFloat(nft.price * settings.currency_rate_dkk)).toFixed(2) }}kr.</span></p>
            </div>
          </div>
          <div class="options-btn__block">
            <button
                class="button-clime-prize"
                data-bs-toggle="modal"
                data-bs-target="#select-buy"
                v-if="flowUser.loggedIn"
            >
              Select and buy
            </button>
            <button>Share</button>
          </div>
          <div class="creator__block">
            <div class="creator-artist" v-if="nft.creator">
              <img v-if="nft.creator_avatar" :src="nft.creator_avatar.attribute_value" alt="logo" />
              <div class="txt">
                <span>Creator</span>
                <p>{{ nft.creator.attribute_value }}</p>
              </div>
            </div>
            <div class="creator-certified">
              <img src="assets/img/logo.png" alt="logo" />
              <div class="txt">
                <span>Certified by</span>
                <p>BEATOKEN</p>
              </div>
            </div>
          </div>
        </div>

        <div class="pack-bottom__block" v-else>
          <div class="owned-block">
            <p>Owned by</p>
            <p class="owner-name" v-if="nft.user">{{ nft.user.name }} <span>(you)</span></p>
          </div>
          <div class="options-btn__block">
            <!--                        <button disabled style="cursor:not-allowed">Share</button>-->

            <template v-if="nft.price">
              <button @click="removeFromSale(nft)">Remove from sale</button>
            </template>
            <template v-else>
              <button v-if="!showDivGift" @click="showDivGift = true">Send as gift</button>
              <template v-if="showDivGift">
                <input class="form-control input-black" v-model="addressRecipient" placeholder="Address recipient" style="margin:0.75rem 0">
                <button @click="showDivGift = false; sendNftAsGift(nft)">Send as Gift</button>
              </template>
              <!--                            <button data-bs-toggle="modal" data-bs-target="#for-sale">Place for sale</button>-->
            </template>
          </div>
          <div class="creator__block">
            <div class="creator-artist" v-if="nft.creator">
              <img v-if="nft.creator_avatar" :src="nft.creator_avatar.attribute_value" alt="logo" />
              <div class="txt">
                <span>Creator</span>
                <p>{{ nft.creator.attribute_value }}</p>
              </div>
            </div>
            <div class="creator-certified">
              <img src="assets/img/logo.png" alt="logo" />
              <div class="txt">
                <span>Certified by</span>
                <p>BEATOKEN</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="pack-details__container">
        <div class="pack-collectible__block">
          <h3 class="desk-title">Collectible details</h3>

          <div class="collectible-block">
            <p class="collectible-txt" v-if="nft.metadata" >
              {{ nft.metadata.description }}
            </p>
          </div>
          <div class="rear-block">
            <div class="rare-set" v-if="nft.pack">
              <p>{{ nft.pack.release.set.name }}</p>
              <p class="released">{{ nft.pack.release.name }} (released <span>{{ true | currentDate }}</span>)</p>
            </div>
          </div>

          <div class="explore-more">
            <img src="assets/img/three-block.svg" alt="block-img" />
            <p>Explore more market data on this NFT edition on
              <a href="#">Beatoken market</a>
            </p>
          </div>
        </div>
        <div class="pack-properties__block">
          <h3 class="desk-title">Properties</h3>
          <div class="properties-items">
            <div class="properties properties-artist" v-if="nft.author">
              <p>Artist</p>
              <p>{{ nft.author.attribute_value }}</p>
            </div>
            <div class="properties properties-type" v-if="nft.type">
              <p>Type</p>
              <p>{{ nft.type.attribute_value }}</p>
            </div>
            <div class="properties properties-type" v-if="nft.numbering">
              <p>Numbering</p>
              <p>{{ nft.numbering.attribute_value }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="section-border"></div>

    <!-- //////////////////// -->
    <!-- MODAL SELECT AND BUY -->
    <!-- //////////////////// -->
    <div
        class="modal fade"
        id="select-buy"
        tabindex="-1"
        aria-labelledby="select-buyModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-content__select-buy">
          <div class="modal-header modal-header__select-buy">
            <h5 class="modal-title" id="select-buyModalLabel">Payment methods</h5>
            <p class="sub-title">
              Choose a convenient payment method from the available
            </p>
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
            <ul class="buy-list">
              <li class="buy-item" style="justify-content:space-between">
                <p class="buy-id">Internal balance</p>
                <p class="buy-price"><span><span>$</span>{{ nft.price | formatPrice }}</span></p>
                <button class="buy-now__btn" @click="buyToken(nft)">Buy now</button>
              </li>
              <li class="buy-item" style="justify-content:space-between">
                <p class="buy-id">Credit card</p>
                <p class="buy-price"><span><span>$</span>{{ nft.price | formatPrice }}</span></p>

                <button class="buy-now__btn" @click="buyByCard(nft)">Buy now</button>
              </li>
            </ul>
          </div>
          <div class="modal-footer modal-footer__modal-footer-buy"></div>
        </div>
      </div>
    </div>

    <!-- ////////////// -->
    <!-- MODAL FOR SALE -->
    <!-- ////////////// -->
    <div
        class="modal fade"
        id="for-sale"
        tabindex="-1"
        aria-labelledby="saleModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="saleModalLabel">Sell your NFT</h5>
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
            <div class="for-sale__manual-block">
              <p>Place your card for sale on the marketplace.</p>
              <p>This is text for browser window after placing the NFT.</p>
              <p>Click "Cancel" if you change your mind.</p>
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
                <p class="desc-name">{{nft.metadata.name}}</p>
                <div class="type-desc">
                  <p>Released <span>{{ true | currentDate }}</span></p>
                </div>

                <div class="tariff-plan" v-if="nft.type">
                  {{ nft.type.attribute_value }} <span class="plan-icon">{{ nft.type.attribute_value.charAt(0) }}</span>
                </div>
              </div>
            </div>
            <div class="for-sale__price-block" style="margin-bottom:unset">
              <money placeholder="Enter price" v-model="nftForSalePrice" />

              <div class="flex-center" style="margin-bottom:1.5rem">
                <span v-if="settings.currency_rate_dkk">({{ Number(parseFloat(nftForSalePrice * settings.currency_rate_dkk)).toFixed(2) }}kr.)</span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="for-sale__btn-block" style="padding-bottom:20px">
              <button @click="putNftForSale(nft)" class="item-success">Place for sale</button>
              <p>To learn more about fees, <a href="#">click here</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel.css'
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css'
import * as fcl from "@onflow/fcl";
import moment from "moment";

export default {
  name: 'single-nft',
  components: {VueSlickCarousel},
  data() {
    return {
      nft: {},
      sliderFor: undefined,
      sliderNav: undefined,
      isCurrentUserOwner: false,
      showDivGift: false,

      addressRecipient: '',
      settings: {},

      nftForSalePrice: '1.00',
    }
  },
  created() {
    this.getAllSettings()
    this.showNft()

    fcl.currentUser().subscribe((flowUser) => {
      if (flowUser.loggedIn) {
        this.$store.commit('flow/setFlowUser', flowUser)
      } else {
        this.$notify({title: 'You must be logged into your blocto account', type: 'error'})
        fcl.authenticate()
      }
    })
  },
  computed: {
    flowUser() {
      return this.$store.getters['flow/getFlowUser']
    },
    user() {
      return this.$store.getters['users/getCurrentUser']
    }
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
            if (this.user.id == nft.user_id) {
              this.isCurrentUserOwner = true
            } else if (this.user.id != nft.user_id && !nft.price) {
              this.$router.push('/marketplace')
            } else if (nft.drops.length) {
              this.$router.push('/drops')
            }
            this.nft = nft

            setTimeout(() => {
              this.sliderFor = this.$refs.sliderFor
              this.sliderNav = this.$refs.sliderNav
            }, 500)

            if (this.$route.query.session_id) this.updatePaymentRefillBalance()
          }).catch(() => this.$router.push('/marketplace'))
    },
    setTrack() {
      var track = {};
      track.name = this.nft.metadata.name;
      if (this.nft.author.attribute_type) track.artist = this.nft.author.attribute_value;
      track.cover = this.nft.metadata.cover_image;
      track.source = this.nft.metadata.source_file;

      this.$store.commit('general/setTracks', [track])
    },
    updatePaymentRefillBalance() {
      this.$loader(true)
      this.$store.dispatch('payments/updatePaymentRefillBalance', {session_id: this.$route.query.session_id})
          .then((resp) => {
            console.log('resp', resp)
            this.$notify({text: 'Congratulations, your balance has been replenished', type: 'success'})
            this.buyToken(this.nft)
          }).catch((e) => {
        console.log('e', e)
        this.$notify({text: 'The session has already expired', type: 'warn'})
        this.$loader(false)
        this.$router.push('/')
      })
    },
    buyByCard(nft){
      var commissionSeller = nft.price / 100 * this.settings.percent_one_token_seller;
      var commissionSum = nft.price / 100 * (Number(this.settings.percent_one_token_seller) + Number(this.settings.percent_one_token_buyer));
      var totalSum = commissionSum + (nft.price - commissionSeller)

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
    buyToken(nft){
      this.$store.dispatch('users/getMiddlemanUser')
          .then((user) => {
            if (this.settings.percent_one_token_seller && this.settings.percent_one_token_buyer && this.settings.percent_author_receive && user) {
              var commissionSeller = nft.price / 100 * this.settings.percent_one_token_seller;
              var commissionSum = nft.price / 100 * (Number(this.settings.percent_one_token_seller) + Number(this.settings.percent_one_token_buyer));

              var commissionAuthor = commissionSum / 100 * this.settings.percent_author_receive;

              this.$loader(true)
              this.$store.dispatch('flow/checkBalance', this.flowUser.addr) // get user balance
                  .then(res => {

                    var balanceUser = res.encodedData.value
                    var totalSum = commissionSum + (nft.price - commissionSeller)

                    if (balanceUser < totalSum) {
                      this.$notify({title: 'You do not have enough funds on your balance to buy this NFT. You can either buy using the card or you can top up the balance in your profile', type: 'error'});
                      this.$loader(false)
                      return false;
                    }

                    this.$store.dispatch('flow/buyTokenInMarketplace', {
                      middleman: user.current_flow_account.address,
                      commission: commissionSum - commissionAuthor,
                      author: nft.author_address,
                      commissionAuthor: commissionAuthor,
                      ownerNft: nft.flow_account.address,
                      flow_id: nft.flow_id,
                      price: nft.price - (commissionSeller)
                    }).then(tx => {
                      this.$notify({title: 'The nft purchase request has been accepted. Processed within a minute'});
                      fcl.tx(tx).subscribe((res) => {
                        if (res.status >= 4 && res.errorMessage == '') {

                          this.$store.dispatch('marketplace/transferNftToMe', {nft_id: nft.id, hash: tx.transactionId, commission: commissionSum - commissionAuthor, price: nft.price - (commissionSeller)})
                              .then(() => { $('#select-buy').modal('hide'); this.$loader(false); this.$router.push('/my-collection') })
                        } else if (res.status >= 4) {
                          console.log('error@res', res)
                          this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                          this.$loader(false)
                        }
                      })
                    }).catch(() => { this.$loader(false); $('#select-buy').modal('hide'); })
                  }).catch(() => { this.$loader(false); $('#select-buy').modal('hide'); })
            } else {
              this.$notify({ title: 'There must be a commission. Please try again later', type: 'error'});
              this.$loader(false)
            }
          })
    },
    sendNftAsGift(nft) {
      if (!this.addressRecipient || this.addressRecipient.length != 18) { this.addressRecipient = ''; this.$notify({title: 'Recipient\'s address contains 18 characters', type: 'warn'}); return false }

      this.$store.dispatch('flow_accounts/getByAddress', this.addressRecipient)
          .then((resp) => {
            if (!resp.id) { this.$notify({title: 'This address is not in our system', type: 'error'}); return false; }

            this.$loader(true)
            this.$store.dispatch('flow/sendNftAsGift', {flowId: nft.flow_id, recipientAddress: this.addressRecipient})
                .then(tx => {
                  fcl.tx(tx)
                      .subscribe((res) => {
                        console.log('res', res)
                        if (res.status >= 4 && res.errorMessage == '') {
                          this.$store.dispatch('nfts/sendNftAsGift', {id: nft.id, recipientAddress: this.addressRecipient})
                              .then(resp => {
                                this.$notify({ title: 'You have successfully presented your NFT to a user with an address ' + this.addressRecipient, type: 'success'});
                                this.$loader(false)
                                this.$router.push('/my-collection')
                              })
                        } else if (res.status >= 4) {
                          console.log('error@res', res)
                          this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                          this.$loader(false)
                        }
                      })
                }).catch(() => this.$loader(false))
          })
    },
    putNftForSale() {
      return false;
      if (!this.nftForSalePrice) {this.$notify({ title: 'You did not enter the nft price', type: 'error' })}
      if (this.settings.percent_one_token_seller) {
        this.$loader(true)

        var priceForFlow = this.nftForSalePrice - (this.nftForSalePrice / 100 * this.settings.percent_one_token_seller); // count the commission

        let nftForSale = {id: this.nft.id, flow_id: this.nft.flow_id, price: priceForFlow}
        this.$store.dispatch('flow/putNftsForSale', nftForSale)
            .then(tx => {
              this.$notify({
                title: 'Your nft has been successfully put up for sale',
                text: 'You need to wait a bit until it appears in the marketplace. It may take about half a minute.',
                type: 'primary'
              });
              fcl.tx(tx)
                  .subscribe((res) => {
                    if (res.status >= 4 && res.errorMessage == '') {
                      this.$store.dispatch('marketplace/setPrice', {id: this.nft.id, price: this.nftForSalePrice})
                          .then(() => {
                            this.$loader(false)
                            $('#for-sale').modal('hide')
                            this.$router.push('/nft-success-place-for-sale/' + this.nft.id)
                          })
                    } else if (res.status >= 4) {
                      console.log('error@res', res)
                      this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                      $('#for-sale').modal('hide')
                      this.$loader(false)
                      this.nftForSalePrice = 0.01
                    }
                  })
            }).catch(() => this.$loader(false))
      } else { this.$notify({ title: 'There must be a commission. Please try again later', type: 'error'}) }
    },
    removeFromSale(nft){
      this.$loader(true)
      this.$store.dispatch('flow/removeFromSale', nft)
          .then(tx => {
            this.$notify({
              title: 'Your nft has been successfully discontinued',
              text: 'You need to wait a bit until it remove from marketplace. This can take about half a minute.',
              type: 'primary'
            });
            fcl.tx(tx)
                // .onceSealed(console.log)
                .subscribe((res) => {
                  if (res.status >= 4 && res.errorMessage == '') {
                    this.$store.dispatch('marketplace/setPrice', {id: nft.id, price: null})
                        .then(() => {
                          this.$loader(false)
                          this.nft.price = ""
                        })
                  } else if (res.status >= 4) {
                    console.log('error@res', res)
                    this.$notify({ title: 'Something went wrong. Please try again later', type: 'error'});
                    this.$loader(false)
                  }
                })
          }).catch(() => this.$loader(false))
    }
  }
}
</script>

<style scoped>
.audio-icon, .video-icon-2 {
  position: absolute;
  opacity: 0.7;
  padding: 7rem;
}
.video-icon-2 {
  top: 0;
  opacity: 0.9;
  padding: 5%;
}
.audio-icon-2, video-icon-2 {
  width: 50% !important;
  margin-left: 20%;
}
</style>