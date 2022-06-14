<template>
    <div>
        <admin-up-header/>

        <div class="container">
            <div class="admin-wrapper">

                <!-- nav -->
                <admin-nav :routes="routes"/>

                <div class="admin-main">

                    <!-- header -->
                    <admin-user-header :user="user" :nft-count-items="nftCountItems"/>

                    <main>
                        <router-view></router-view>
                    </main>
                </div>
            </div>
        </div>

        <modal-refill :user="user"/>
        <modal-reset-password :user="user"/>
    </div>
</template>

<script>

import AdminNav from '../AdminNav'
import AdminUpHeader from '../AdminUpHeader'
import AdminUserHeader from './AdminUserHeader'
import ModalResetPassword from "../../admin-modals/ModalResetPassword";
import ModalRefill from "../../admin-modals/ModalRefill";

export default {
    name: 'AdminUserMain',
    components: {
        ModalRefill,
        ModalResetPassword,
        AdminNav,
        AdminUpHeader,
        AdminUserHeader,
    },
    data() {
        return {
            routes: [],
            nftCountItems: 0
        }
    },
    created() {
        this.getUser()
            .then((user) => {
                this.nftCountItems = user.nfts.length

                this.getRoles()
                this.checkBalance(user)

                this.routes = [
                    {to: '/admin/users/', title: '<i class="icon-arrow-left"></i>'},
                    {to: '/admin/user/' + user.id + '/profile', title: 'Profile'},
                    {to: '/admin/user/' + user.id + '/nft', title: 'NFT'},
                    {to: '/admin/user/' + user.id + '/balance-history', title: 'Balance history'}
                ]
        })
    },
    computed: {
        user() {
            return this.$store.getters['users/getUser']
        }
    },
    methods: {
        getUser() {
            return this.$store.dispatch('users/getUser', this.$route.params.user_id)
        },
        getRoles() {
            this.$store.dispatch('roles/allRoles')
        },
        checkBalance(user) {
            if (!user.current_flow_account || user.balance) return false

            this.$store.dispatch('flow/checkBalance', user.current_flow_account.address)
                .then((resp) => {
                    const user = Object.assign({}, this.user)
                    user.balance = resp.encodedData.value

                    this.$store.commit('users/setUser', user)
                })
        }
    }
}
</script>
