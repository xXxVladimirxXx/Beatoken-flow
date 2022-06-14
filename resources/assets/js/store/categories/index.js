import axios from 'axios'

export default {
    namespaced: true,
    actions: {
        getCategories({}) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/categories',
                    method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                },err => {
                    reject(err.response)
                })
            })
        },
        getPacks({}) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/categories/get-packs',
                    method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                },err => {
                    reject(err.response)
                })
            })
        },
        addSet({}, set) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/categories/create-set',
                    method: 'POST',
                    data: set,
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        },
        addRelease({}, release) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/categories/create-release',
                    method: 'POST',
                    data: release,
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        },
        addPack({}, pack) {
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/categories/create-pack',
                    method: 'POST',
                    data: pack,
                    headers: { "Content-Type": "multipart/form-data" },
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        },
    }
};
