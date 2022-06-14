<template>
    <div>
        <h3 class="admin-table__title">User's NFT collection</h3>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col">Cover img</th>
                <th scope="col">Set</th>
                <th scope="col">Price</th>
                <th scope="col">On sale</th>
            </tr>
            </thead>
            <tbody>
                <tr v-if="nfts.length && item.metadata" v-for="(item, i) in nfts">
                    <td class="table-nft__img">
                        <img
                            :src="item.metadata.cover_image"
                             onerror="this.hidden=true"/>

                        <video
                            :src="item.metadata.cover_image"
                            muted
                            autoplay
                            width="100"
                            height="100"
                            onerror="this.hidden=true"/>
                    </td>
                    <td class="table-collection">
                        <p v-if="item.pack" v-html="$options.filters.formatPack(item.pack)"></p>
                        <p v-else>Does not belong to the pack</p>
                    </td>
                    <td>
                        {{ item.price ? item.price + ' tokens' : 'Not for sale' }}
                    </td>
                    <td><p>{{ item.price ? 'Yes' : 'No' }}</p></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: 'UserNft',
    data() {
        return {
            nfts: [],
            user: {},
        }
    },
    created() {
        this.getUser(this.$route.params.user_id)
    },
    filters: {
        formatPack(pack) {
            return `
            <p>Set: <span>`+ pack.release.set.name +`</span></p>
            <p>Release: <span>`+ pack.release.name +`</span></p>
            <p>Pack: <span>`+ pack.name +`</span></p>`
        }
    },
    computed: {
        flowUser() {
            return this.$store.getters['flow/getFlowUser']
        }
    },
    methods: {
        allNftsByUserId() {
            this.$store.dispatch('nfts/allNftsByUserId', this.$route.params.user_id)
                .then(nfts => {
                    console.log('nfts', nfts)
                    this.nfts = nfts
                })
        },
        getUser(user_id) {
            this.$store.dispatch('users/getUser', user_id)
                .then(user => {
                    this.user = user
                    this.syncAllNFTs(this.user.current_flow_account.address)
                    this.allNftsByUserId()
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

                                        this.$store.dispatch('nfts/synchronizationNfts', {nfts: nftsForSync, address: addr})
                                    })
                            } else {
                                this.$store.dispatch('nfts/synchronizationNfts', {nfts: nftsForSync, address: addr})
                            }
                        }).catch((err) => {
                            console.log('allNftsForSale@err', err)
                            this.$store.dispatch('nfts/synchronizationNfts', {nfts: nftsForSync, address: addr})
                        })
                }).catch((err) => {
                console.log('allNfts@err', err)
            })
        },
    }
}
</script>
