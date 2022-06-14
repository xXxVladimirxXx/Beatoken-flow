import axios from 'axios'

export default {
    namespaced: true,
    actions: {
        index({}) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/withdrawal-requests", method: "GET" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        create({}, data) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/withdrawal-requests", data, method: "POST" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        show({}, withdrawal_request_id) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/withdrawal-requests/" + withdrawal_request_id, method: "GET" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        update({}, data) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/withdrawal-requests/" + data.id, data, method: "PUT" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        }
    }
};
