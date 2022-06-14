<template>
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
          <div class="music-card__container">
            <div class="empty-collection">
              <div v-if="!flowUser.loggedIn">
                <p class="mb my-balance">Login to your flow account</p>
                <button @click="authenticate" class="btn btn-primary">Authenticate</button>
              </div>
              <div v-else>
                <p class="balance-container__title">Wallet</p>
                <div class="balance-container">
                  <div class="balance-block__input">
                    <p class="balance-title">Your flow address:</p>
                    <div class="my-balance__block">
                      <p class="my-balance address" >{{ flowUser.addr }}</p>
                    </div>


                    <div class="btn-balance__block">
                      <button @click="unauthenticateFcl" class="btn-balance">Disconnect</button>
                    </div>
                  </div>

                  <div class="balance-block__input" v-if="flowUser.balance">
                    <p class="balance-title">Balance:</p>
                    <div class="my-balance__block">
                      <p class="my-balance">{{ flowUser.balance | formatBalance}}</p>
                    </div>
                    <div class="btn-balance__block">
                      <button class="btn-balance" style="margin-right: 20px" data-bs-toggle="modal" data-bs-target="#modal-withdrawal-balance">Withdrawal</button>
                      <button class="btn-balance" data-bs-toggle="modal" data-bs-target="#refill-balance-by-card">Refill balance</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>

        <!-- CONNECT BLOCTO ACCOUNT MODAL -->
        <div
            class="modal fade"
            id="blockto-account"
            tabindex="-1"
            aria-hidden="true"
        >
          <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content modal-content-blocto">
              <div class="modal-header modal-header-blockto">
                <h5 class="modal-title blockto-title">Connect your blocto account</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                >
                  <i class="icon-btn-close"></i>
                </button>
              </div>
              <div class="modal-body blockto">
                <div class="blocto-block" style="width:100%">
                  <p class="number">1</p>
                  <div class="check">
                    <div class="box active">
                    </div>
                  </div>
                  <p class="blockto-text">Your wallet is successfully authorized in our system</p>
                </div>
                <div class="blocto-block" style="width:100%">
                  <p class="number">2</p>
                  <div class="check">
                    <div class="box" v-bind:class="{active: flowUser.balance}">
                      <div class="spinner" v-if="!flowUser.balance"></div>
                    </div>
                  </div>
                  <p class="blockto-text">Now we need to create empty storages so that you can replenish your account, create and buy NFT’s</p>
                </div>
              </div>
              <div class="modal-footer blockto-footer">
                <button class="btn-finish" data-bs-dismiss="modal">Finish</button>
                <span class="line"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- -->

        <refill-balance-by-card />
        <create-nft />
        <withdrawal />
      </div>
    </div>
    <div class="section-border"></div>
  </div>
</template>

<script>
import * as fcl from "@onflow/fcl"
import moment from "moment";
import RefillBalanceByCard from "./components/modals/RefillBalanceByCard";
import CreateNft from "./components/modals/CreateNft";
import Withdrawal from "./components/modals/Withdrawal";

export default {
  name: 'Profile',
  components: { RefillBalanceByCard, CreateNft, Withdrawal },
  data() {
    return {}
  },
  created() {
    this.getProfileData()
  },
  filters: {
    formatDate(date) {
      return moment(date).format('MMM DD, YYYY')
    },
    formatBalance(balance) {
      balance = parseFloat(balance).toFixed(2)

      return balance + ' BLUcoin' + '\xa0\xa0\xa0\xa0\xa0\xa0\xa0' +  '(' + balance + '$)'
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
    authenticate() {
      return fcl.authenticate()
    },
    unauthenticateFcl() {
      this.$store.commit('flow/setFlowUser', {})
      return fcl.unauthenticate()
    },
    getProfileData() {
      return this.$store.dispatch('users/getCurrentUser')
          .then((user) => {
            this.authenticate()
            fcl.currentUser().subscribe((flowUser) => {
              if (flowUser.loggedIn) {
                if (user.current_flow_account == null ||
                    user.current_flow_account.address != flowUser.addr ||
                    user.current_flow_account.email != flowUser.services[0].scoped.email) {

                  this.$store.dispatch('flow_accounts/storeFlowAccount', {address: flowUser.addr, email: flowUser.services[0].scoped.email})
                }

                flowUser.balance = ''
                this.$store.commit('flow/setFlowUser', flowUser)
                this.$store.dispatch('flow/checkBalance', flowUser.addr)
                    .then(res => {
                      flowUser.balance = res.encodedData.value
                      this.$store.commit('flow/setFlowUser', flowUser)

                      this.syncAllNFTs(flowUser.addr)
                    }).catch(err => {
                  console.log(err)
                  this.createEmptyStorages(flowUser)
                })
              }
            })
          })
    },
    syncAllNFTs(addr) {
      this.$store.dispatch('flow/allNfts', addr)
          .then((nfts) => {

            let nftsForSync = []

            if (nfts) {
              Object.keys(nfts).forEach((id) => {
                nftsForSync.push({id: id, token_uri: nfts[id].metadata.token_uri})
              })
            }

            this.$store.dispatch('flow/allNftsForSale', addr)
                .then((nftsInSale) => {
                  if (nftsInSale) {

                    let ids = []
                    Object.keys(nftsInSale).forEach((key) => {
                      ids.push(nftsInSale[key].id)
                    })

                    this.$store.dispatch('flow/getPriceByIds', {flow_addr: addr, ids: ids})
                        .then(prices => {
                          Object.keys(nftsInSale).forEach((id) => {
                            nftsForSync.push({id: id, token_uri: nftsInSale[id].metadata.token_uri, price: Number(parseFloat(prices[id])).toFixed(2)})
                          })

                          this.$store.dispatch('nfts/synchronizationNfts', {nfts: nftsForSync})
                        })
                  } else {
                    this.$store.dispatch('nfts/synchronizationNfts', {nfts: nftsForSync})
                  }
                }).catch(() => {
              this.$store.dispatch('nfts/synchronizationNfts', {nfts: nftsForSync})
            })
          }).catch((err) => {
        console.log('allNfts@err', err)
      })
    },
    createEmptyStorages(flowUser) {
      $('#blockto-account').modal('show')
      this.$store.dispatch('flow/createEmptyStorages')
          .then(tx => {
            this.$loader(true)
            fcl.tx(tx)
                .subscribe(res => {
                  if (res.status >= 4 && res.errorMessage == '') {
                    this.$store.dispatch('flow/checkBalance', flowUser.addr)
                        .then((res) => {
                          this.$notify({title: 'Your account is fully set up', type:'success'})
                          flowUser.balance = res.encodedData.value
                          this.$store.commit('flow/setFlowUser', flowUser)
                          this.$loader(false)
                        }).catch(() => this.$loader(false))
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

<style>
.label-photo {
  border: 2px solid #8b23e7;
  color: white;
  background-color: #8b23e7;
  padding: 0px 10px;
  border-radius: 8px;
  font-size: 20px;
  font-weight: bold;
}
</style>
