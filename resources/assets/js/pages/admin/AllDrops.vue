<template>
    <div>
        <h3 class="admin-table__title">User's Drop collection</h3>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col">Cover img</th>
                <th scope="col">Start</th>
                <th scope="col">End</th>
                <th scope="col">Price</th>
                <th scope="col">Count Nft</th>
                <th scope="col">Owned</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="drops.length"  v-for="(item, i) in drops">
                <td class="table-nft__img">
                    <img :src="item.full_uri" onerror="this.hidden=true"/>
                </td>
                <td><small>{{ item.release_start | formatDate }}</small></td>
                <td><small>{{ item.release_end | formatDate }}</small></td>
                <td><small>{{ item.price ? item.price + 'kr' : 'Not for sale' }}</small></td>
                <td><small>{{ item.nfts.length }}</small></td>
                <td><small>{{ item.user.name }}</small></td>
                <td><small>{{ item.status }}</small></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: 'UserNft',
    data() {
        return {
            drops: [],
        }
    },
    created() {
        this.allDrops()
    },
    filters: {
        formatDate(date) {
            return moment(date).format('DD.MM.YYYY, HH:mm')
        }
    },
    methods: {
        allDrops() {
            this.$store.dispatch('drops/allDrops')
                .then(drops => {
                    this.drops = drops
                })
        },
    }
}
</script>
