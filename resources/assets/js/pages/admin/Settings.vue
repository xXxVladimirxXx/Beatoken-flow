<template>
    <div>
        <h3 class="admin-table__title">Nft settings</h3>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col">Description</th>
                <th scope="col">Value</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Percentage of commission when selling through a drop</td>
                    <td class="flex-center">
                        <div>Seller <input v-model="form.percent_drop_seller" min="0" class="form-control" type="number"/></div>
                        <div>Buyer <input v-model="form.percent_drop_buyer" min="0" class="form-control" type="number"/></div>
                    </td>
                </tr>
                <tr>
                    <td>Percentage of commission for the sale of one token</td>
                    <td class="flex-center">
                        <div>Seller <input v-model="form.percent_one_token_seller" min="0" class="form-control" type="number"/></div>
                        <div>Buyer <input v-model="form.percent_one_token_buyer" min="0" class="form-control" type="number"/></div>
                    </td>
                </tr>
                <tr>
                    <td>What percentage the author will receive. Remaining commission go to middleman</td>
                    <td class="flex-center">
                        <div>Author <input v-model="form.percent_author_receive" min="0" class="form-control" type="number"/></div>
                    </td>
                </tr>
                <tr>
                    <td>DKK currency rate</td>
                    <td><input v-model="form.currency_rate_dkk" min="0" class="form-control" type="number"/></td>
                </tr>
                <tr>
                    <td>The waiting time for the line and the time that is given for the purchase of a token</td>
                    <td class="flex-center">
                        <div>Time line (sec) <input v-model="form.default_time_drop_line" min="0" class="form-control" type="number"/></div>
                        <div>Buy drop (sec) <input v-model="form.default_time_drop_payment" min="0" class="form-control" type="number"/></div>
                    </td>
                </tr>
                <tr>
                    <td>The number of keys used to sign the FLOW transactions.
                        <span style="color:#f86767">DO NOT CHANGE THE VALUE UNLESS THE cadency/flow.json FILE HAS BEEN UPDATED</span>
                    </td>
                    <td class="flex-center">
                        <div>Keys number <input v-model="form.keys_number" min="0" class="form-control" type="number"/></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="flex flex-center">
            <button @click="saveSettings" class="btn btn-success"><h3>Save</h3></button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Settings',
    data() {
        return {
            form: {
                percent_drop_seller: '',
                percent_drop_buyer: '',
                percent_one_token_seller: '',
                percent_one_token_buyer: '',
                percent_author_receive: '',
                currency_rate_dkk: '',
                default_time_drop_line: '',
                default_time_drop_payment: '',
                keys_number: ''
            }
        }
    },
    created() {
        this.allSettings()
    },
    methods: {
        allSettings() {
            this.$store.dispatch('settings/allSettings')
                .then(settings => {
                    this.form = settings
                })
        },
        saveSettings() {
            this.$store.dispatch('settings/addSettings', this.form)
                .then(settings => {
                    this.form = settings
                })
        }
    }
}
</script>
