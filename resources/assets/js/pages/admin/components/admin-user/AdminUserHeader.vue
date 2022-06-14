<template>
    <div>
        <div class="admin-main__nav">
            <div class="user-data__block">
                <div class="user-data__avatar">
                    <img :src="user.full_uri_avatar" alt="user-avatar" />
                </div>
                <div class="user-data">
                    <div class="user-data__txt">
                        <p>{{user.name}}</p>
                        <p>{{user.email}}</p>
                        <small>Flow address: <span v-if="user.current_flow_account">{{user.current_flow_account.address}}</span></small>
                    </div>

                    <div class="user-refill">
                        <p>Balance:<span v-if="user.balance">{{parseFloat(user.balance).toFixed(2)}} tk.</span></p>
                        <button
                            data-bs-toggle="modal"
                            data-bs-target="#refill"
                            class="refill-btn">

                            Refill balance
                        </button>
                    </div>
                </div>
            </div>
            <div class="user-nft__block">
                <p v-if="nftCountItems" class="nft-sum">NFT:<span>{{nftCountItems}}</span>items</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AdminUserHeader',
    props: {
        user: {
            type: Object,
            required: false
        },
        nftCountItems: {
            type: Number,
            required: false
        }
    }
}
</script>
