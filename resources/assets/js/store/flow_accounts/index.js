import axios from 'axios'

export default {
    namespaced: true,
    state: {},
    actions: {
        allFlowAccounts({}) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/flow-accounts',
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        },
        storeFlowAccount({}, flowAccount) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/flow-accounts',
                  method: 'POST',
                  data: flowAccount,
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        },
        getByAddress({}, address) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/flow-accounts/get-by-address/' + address,
                    method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        },
        showFlowAccount({}, flow_account_id) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/flow-accounts/' + flow_account_id,
                    method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        }
    },
    mutations: {},
    getters: {}
};
