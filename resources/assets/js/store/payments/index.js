import axios from 'axios'

export default {
    namespaced: true,
    actions: {
        throughStripeGateway({}, data) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/payments/through-stripe-gateway", data, method: "POST" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        updatePaymentRefillBalance({}, data) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/payments/update-payment-refill-balance", data, method: "POST" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
    }
};
