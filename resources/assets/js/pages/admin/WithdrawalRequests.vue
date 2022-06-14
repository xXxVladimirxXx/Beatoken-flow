<template>
    <div>
        <admin-up-header/>

        <div class="container">
            <div class="admin-wrapper">
                <div class="admin-nav">
                    <ul class="admin-nav__list">
                        <li class="admin-nav__item"><router-link to="/admin/users">Users</router-link></li>
                        <li class="admin-nav__item"><router-link to="/admin/withdrawal-requests">Withdrawal requests</router-link></li>
                        <li class="admin-nav__item"><router-link to="/admin/nfts/all-nfts">Beatoken NFT</router-link></li>
                    </ul>
                </div>

                <div class="admin-main">
                    <div class="admin-main__nav">
                        <div class="admin-main__title-block">
                            <h3 class="admin-main__title">Withdrawal requests</h3>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">№</th>
                            <th scope="col">User email</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Link to payments</th>
                            <th scope="col">Status</th>
                            <th scope="col">Date create</th>
                        </tr>
                        </thead>
                        <tbody>
                            <template v-for="item in withdrawal_requests">
                                <tr>
                                    <th scope="row">{{item.id}}</th>
                                    <td><small>{{item.user.email}}</small></td>
                                    <td><small>{{item.amount}}</small></td>
                                    <td><small><a style="text-decoration:underline" :href="'https://dashboard.stripe.com/search?query='+item.user.email+'%20is%3Apayment%20sort%3A-created'">Link</a></small></td>
                                    <td v-bind:class="{ pending: 'pending' == item.status, done: 'done' == item.status, rejected: 'rejected' == item.status }">
                                        <select class="form-control" v-model="item.status" @change="updateStatus(item)">
                                            <option v-for="(status, i) in statuses" :value="status">{{status}}</option>
                                        </select>
                                    </td>
                                    <td><small>{{item.created_at | formatDate}}</small></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";
import AdminUpHeader from './components/AdminUpHeader'

export default {
    name: 'withdrawal-requests',
    components: {
        AdminUpHeader
    },
    data() {
        return  {
            withdrawal_requests: {},
            statuses: [
                'pending',
                'done',
                'rejected'
            ]
        }
    },
    created() {
        this.getAllWithdrawalRequests()
    },
    filters: {
        formatDate(date) {
            return moment(String(date)).format('DD.MM.YYYY')
        }
    },
    methods: {
        getAllWithdrawalRequests() {
            this.$store.dispatch('withdrawal_requests/index')
                .then((resp) => {
                    this.withdrawal_requests = resp
                })
        },
        updateStatus(item) {
            this.$store.dispatch('withdrawal_requests/update', {id: item.id, status: item.status})
        }
    }
}
</script>

<style scoped>
.form-control {
    opacity: 0.7;
}
.table-bordered .pending {
    background-color: gray;
}
.table-bordered .done {
    background-color: green;
}
.table-bordered .rejected {
    background-color: red;
}
</style>
