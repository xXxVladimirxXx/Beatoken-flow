import axios from 'axios'
import { parseMetaAttributes } from '../../mixins/nfts'

export default {
    namespaced: true,
    actions: {
        allNfts({}) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/nfts',
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data.map(nft => parseMetaAttributes(nft)))
                }, err => {
                    reject(err.response)
                })
              })
        },
        allNftsOfAllUsers({}) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/nfts/all-nfts-of-all-users',
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data.map(nft => parseMetaAttributes(nft)))
                }, err => {
                    reject(err.response)
                })
              })
        },
        allNftsByUserId({}, user_id) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/nfts/by-user-id/' + user_id,
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data.map(nft => parseMetaAttributes(nft)))
                }, err => {
                    reject(err.response)
                })
              })
        },
        allNftsForSale({}) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/nfts/nfts-for-sale',
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data.map(nft => parseMetaAttributes(nft)))
                }, err => {
                    reject(err.response)
                })
              })
        },
        storeNft({}, nft) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/nfts',
                  method: 'POST',
                  data: nft,
                  headers: { "Content-Type": "multipart/form-data" },
                }).then(resp => {
                    resolve(resp.data.map(nft => parseMetaAttributes(nft)))
                }, err => {
                    reject(err.response)
                })
              })
        },
        sendNftAsGift({}, {id, recipientAddress}) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/nfts/send-as-gift/'+id+'/'+recipientAddress,
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        },
        synchronizationNfts({}, {nfts, address}) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/nfts/synchronization-nfts',
                    method: 'POST',
                    data: {nfts, address}
                }).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
              })
        },
        showNft({}, nft_id) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/nfts/' + nft_id,
                  method: 'GET',
                }).then(resp => {
                    resolve(parseMetaAttributes(resp.data))
                }, err => {
                    reject(err.response)
                })
              })
        }
    }
};
