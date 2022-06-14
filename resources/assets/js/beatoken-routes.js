import VueRouter from "vue-router"

import {MetaCollection} from './meta-collection'

const main = () => import('./pages/components/Main')

//  For unauthorized user
const drops = () => import('./pages/Drops')
const drop = () => import('./pages/Drop')
const dropLine = () => import('./pages/DropLine')
const dropBuy = () => import('./pages/DropBuy')
// const dropOpen = () => import('./pages/DropOpen')
const collaborate = () => import('./pages/Collaborate')
const marketplace = () => import('./pages/Marketplace')

const signup = () => import('./pages/Signup')
const verification = () => import('./pages/Verification')
const login = () => import('./pages/Login')
const restore = () => import('./pages/Restore')
const resetPassword = () => import('./pages/ResetPassword')

//  For authorized user
const logged = () => import('./pages/Logged')
const profile = () => import('./pages/Profile')
const editProfile = () => import('./pages/EditProfile')
const singleNft = () => import('./pages/SingleNft')
const nftSuccessPlaceForNftSale = () => import('./pages/NftSuccessPlaceForNftSale')
const myCollection = () => import('./pages/MyCollection')
const successPayment = () => import('./pages/SuccessPayment')
const cancelPayment = () => import('./pages/CancelPayment')

// For users
const adminUserMain = () => import('./pages/admin/components/admin-user/AdminUserMain')
const editUser = () => import('./pages/admin/EditUser')
const userNft = () => import('./pages/admin/UserNft')
const balanceHistory = () => import('./pages/admin/BalanceHistory')
const allUsers = () => import('./pages/admin/AllUsers')

const withdrawalRequests = () => import('./pages/admin/WithdrawalRequests')

// For Admin nfts
const adminNftsMain = () => import('./pages/admin/components/admin-nft/AdminNftsMain')
const allNfts = () => import('./pages/admin/AllNfts')
const allDrops = () => import('./pages/admin/AllDrops')
const categories = () => import('./pages/admin/Categories')
const settings = () => import('./pages/admin/Settings')

const notFound = () => import('./pages/NotFound')

export default {
    routes: new VueRouter({
        mode: 'history',
        routes: [
            {
                path: '',
                component: main,
                children: [

                    /* For unauthorized user */
                    {
                        path: '/collaborate',
                        name: 'collaborate',
                        component: collaborate
                    },

                    /* For authorized simple user */
                    {
                        path: '/',
                        name: 'profile',
                        component: profile,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/drops',
                        name: 'drops',
                        component: drops,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/drop/:drop_id',
                        name: 'drop',
                        component: drop,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/drop-line/:drop_id',
                        name: 'dropLine',
                        component: dropLine,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/drop-buy/:drop_id',
                        name: 'dropBuy',
                        component: dropBuy,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/marketplace',
                        name: 'marketplace',
                        component: marketplace,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/logged',
                        name: 'logged',
                        component: logged,
                        meta: MetaCollection.USER
                    },
                    {
                        path: 'edit-profile',
                        name: 'editProfile',
                        component: editProfile,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/nft/:nft_id',
                        name: 'nft',
                        component: singleNft,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/nft-success-place-for-sale/:nft_id',
                        name: 'nft-success-place-for-sale',
                        component: nftSuccessPlaceForNftSale,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/my-collection',
                        name: 'myCollection',
                        component: myCollection,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/success-payment',
                        name: 'successPayment',
                        component: successPayment,
                        meta: MetaCollection.USER
                    },
                    {
                        path: '/cancel-payment',
                        name: 'cancelPayment',
                        component: cancelPayment,
                        meta: MetaCollection.USER
                    }
                ],
            },
            // Another layout
            {
                path: '/signup',
                name: 'signup',
                component: signup
            },
            {
                path: '/verification',
                name: 'verification',
                component: verification
            },
            {
                path: '/login',
                name: 'login',
                component: login
            },
            {
                path: '/resend',
                name: 'restore',
                component: restore
            },
            {
                path: '/reset-pass/:hash',
                name: 'resetPassword',
                component: resetPassword
            },

            // For withdrawal requests
            {
                path: '/admin/withdrawal-requests',
                name: 'withdrawalRequests',
                component: withdrawalRequests,
                meta: MetaCollection.SUPERADMIN
            },

            // For users
            {
                path: '/admin/users',
                name: 'allUsers',
                component: allUsers,
                meta: MetaCollection.SUPERADMIN
            },
            {
                path: '/admin/user/:user_id/',
                component: adminUserMain,
                meta: MetaCollection.SUPERADMIN,
                children: [
                    {
                        path: 'profile',
                        name: 'editUser',
                        component: editUser,
                        meta: MetaCollection.SUPERADMIN
                    },
                    {
                        path: 'nft',
                        name: 'userNft',
                        component: userNft,
                        meta: MetaCollection.SUPERADMIN
                    },
                    {
                        path: 'balance-history',
                        name: 'balanceHistory',
                        component: balanceHistory,
                        meta: MetaCollection.SUPERADMIN
                    },
                ]
            },

            {
                path: '/admin/nfts/',
                component: adminNftsMain,
                meta: MetaCollection.SUPERADMIN,
                children: [
                    {
                        path: 'all-nfts',
                        name: 'allNfts',
                        component: allNfts,
                        meta: MetaCollection.SUPERADMIN
                    },
                    {
                        path: 'all-drops',
                        name: 'allDrops',
                        component: allDrops,
                        meta: MetaCollection.SUPERADMIN
                    },
                    {
                        path: 'categories',
                        name: 'categories',
                        component: categories,
                        meta: MetaCollection.SUPERADMIN
                    },
                    {
                        path: 'settings',
                        name: 'settings',
                        component: settings,
                        meta: MetaCollection.SUPERADMIN
                    }
                ]
            },


            {
                path: '',
                component: main,
                children: [
                    {
                        path: "*",
                        name: '404',
                        component: notFound
                    }
                ]
            }
        ]
    })
}
