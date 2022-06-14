import axios from 'axios'

export default {
    namespaced: true,
    actions: {
        allDrops() {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/drops',
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        },
        storeDrop({}, drop) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/drops',
                  method: 'POST',
                  data: drop,
                  headers: { "Content-Type": "multipart/form-data" },
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        },
        buyDrop({}, data) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/drops/buy-drop/' + data.id,
                  method: 'POST',
                  data
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        },
        getCurrentDrop() {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/drops/get-current-drop',
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        },
        showDrop({}, drop_id) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/drops/' + drop_id,
                  method: 'GET',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        },
        update({}, drop) {
            console.log('drop', drop)
            return new Promise((resolve, reject) => {
                axios({
                    url: '/api/drops/' + drop.id,
                    method: 'PUT',
                    data: drop,
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
            })
        },
        delete({}, drop_id) {
            return new Promise((resolve, reject) => {
                axios({
                  url: '/api/drops/' + drop_id,
                  method: 'DELETE',
                }).then(resp => {
                    resolve(resp.data)
                }, err => {
                    reject(err.response)
                })
              })
        }
    }
};
