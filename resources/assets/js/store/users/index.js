import axios from 'axios'
import * as fcl from "@onflow/fcl";

const bufToken = localStorage.getItem('user-token')
const bufUser = JSON.parse(localStorage.getItem('currentUser'))

if (bufToken) {
    axios.defaults.headers.common.Authorization = `Bearer ${bufToken}`
}

export default {
    namespaced: true,
    state: {
        token: bufToken || "",
        currentUser: bufUser || {},
        user: {}
    },
    actions: {
        login({ commit }, user) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/login", data: user, method: "POST" })
                    .then(resp => {
                        const token = resp.data.token
                        const currentUser = resp.data.user

                        localStorage.setItem("user-token", token)
                        localStorage.setItem("currentUser", JSON.stringify(currentUser))
                        axios.defaults.headers.common.Authorization = `Bearer ${token}`
                        commit("setToken", token)
                        commit("setCurrentUser", currentUser)

                        resolve(resp.data)
                    })
                    .catch(err => {
                        localStorage.removeItem("user-token")
                        localStorage.removeItem("currentUser")
                        reject(err.response)
                    });
            });
        },
        register({}, user) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/register", data: user, method: "POST" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        getAllUsers() {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users", method: "GET" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        changeAvatar({}, {formData, user_id}) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/change-avatar/" + user_id, data: formData, method: "POST", headers: { "Content-Type": "multipart/form-data" } })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        changePassword({}, {data, user_id}) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/change-password/" + user_id, data: data, method: "POST"})
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        update({}, user) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/" + user.id, data: user, method: "PUT" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        getCurrentUser({ commit }) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/current-user", method: "GET" })
                    .then(resp => {
                        localStorage.setItem("currentUser", JSON.stringify(resp.data))
                        commit("setCurrentUser", resp.data)
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        getUser({ commit }, user_id) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/" + user_id, method: "GET" })
                    .then(resp => {
                        commit("setUser", resp.data)
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        getMiddlemanUser() {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/get-middleman-user", method: "GET" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        getWithdrawManagerUser() {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/get-withdraw-manager-user", method: "GET" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        refillBalance({}, {user, amount}) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/refill-balance/" + user.id, data: {amount}, method: "POST" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        refillBalanceByCard({}, data) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/users/refill-balance-by-card", data, method: "POST" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        forgotPassword({}, email) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/forgot-password/" + email, method: "GET" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response.data)
                    })
            });
        },
        verifyHash({}, token) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/check-token", method: "POST", data: {token} })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response.data)
                    })
            });
        },
        changePasswordWhenForgot({}, data) {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/reset/process", method: "POST", data: data })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response.data)
                    })
            });
        },
        resend() {
            return new Promise((resolve, reject) => {
                axios({ url: "/api/email/resend", method: "GET" })
                    .then(resp => {
                        resolve(resp.data)
                    }, err => {
                        reject(err.response)
                    })
            });
        },
        logout({ commit }) {
            axios({ url: "/logout", method: "GET" })
            localStorage.removeItem("user-token")
            localStorage.removeItem("currentUser")
            commit("setToken", "")
            commit("setCurrentUser", {})
            fcl.unauthenticate()
            document.location.reload();
        }
    },
    mutations: {
        setToken(state, token) {
            state.token = token
        },
        setCurrentUser(state, currentUser) {
            state.currentUser = currentUser
        },
        setUser(state, user) {
            state.user = user
        }
    },
    getters: {
        getCurrentUser(state) {
            return state.currentUser
        },
        getUser(state) {
            return state.user
        }
    }
};
