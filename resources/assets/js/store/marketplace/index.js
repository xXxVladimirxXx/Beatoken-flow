import axios from 'axios'

export default {
    namespaced: true,
    actions: {
        setPrice({}, nft) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/marketplace/set-price/' + nft.id,
                    method: 'POST',
                    data: {price: nft.price},
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        },
        transferNftToMe({}, data) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/marketplace/transfer-nft-to-me/' + data.nft_id,
                    method: 'POST',
                    data: data
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        }
    }
};
