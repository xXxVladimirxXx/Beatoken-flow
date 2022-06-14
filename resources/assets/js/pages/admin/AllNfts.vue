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
                <th scope="col">Owner</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="nfts.length"  v-for="(item, i) in nfts">
                <td class="table-nft__img" v-if="item.metadata">
                    <img :src="item.metadata.cover_image"/>
                </td>
                <td class="table-collection">
                    <p v-if="item.pack" v-html="$options.filters.formatPack(item.pack)"></p>
                    <p v-else>Does not belong to the pack</p>
                </td>
                <td><small>{{ item.price ? item.price + ' tokens' : 'Not for sale' }}</small></td>
                <td><small>{{ item.price ? 'Yes' : 'No' }}</small></td>
                <td><small v-if="item.user">{{ item.user.name }}</small></td>
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
        }
    },
    created() {
        this.allNftsOfAllUsers()
    },
    filters: {
        formatPack(pack) {
            return `
            <p>Set: <span>`+ pack.release.set.name +`</span></p>
            <p>Release: <span>`+ pack.release.name +`</span></p>
            <p>Pack: <span>`+ pack.name +`</span></p>`
        }
    },
    methods: {
        allNftsOfAllUsers() {
            this.$store.dispatch('nfts/allNftsOfAllUsers')
                .then(nfts => {
                    this.nfts = nfts
                })
        },
    }
}
</script>
