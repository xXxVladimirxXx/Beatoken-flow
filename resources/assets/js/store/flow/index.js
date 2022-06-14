import * as fcl from "@onflow/fcl";
import * as t from "@onflow/types";

export default {
    namespaced: true,
    state: {
        nfts: {},
        flowUser: {
            loggedIn: false
        }
    },
    actions: {
        /* For profile */
        createEmptyStorages({ state }) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.transaction(`
                    import FungibleToken from 0xAccountFungibleToken
                    import FungibleBeatoken from 0xAccountFungibleBeatoken
                    import NonFungibleToken from 0xAccountNonFungibleToken
                    import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken

                    transaction {
                        prepare(acct: AuthAccount) {
                            let vaultPath = FungibleBeatoken.vaultStoragePath
                            let publicPath = FungibleBeatoken.publicReceiverPath

                            let storageCollection =  NonFungibleBeatoken.storageCollection

                            acct.save(<-FungibleBeatoken.createEmptyVault(), to: vaultPath)

                            acct.link<&FungibleBeatoken.Vault{FungibleToken.Receiver, FungibleToken.Balance}>
                                 (publicPath, target: vaultPath)

                            acct.save<@NonFungibleBeatoken.Collection>(<-NonFungibleBeatoken.createEmptyCollection(), to: storageCollection)

                            acct.link<&NonFungibleBeatoken.Collection{NonFungibleToken.CollectionPublic, NonFungibleBeatoken.BeatokenCollectionPublic}>(NonFungibleBeatoken.publicReceiver, target: storageCollection)
                        }
                    }
                `),
                    fcl.payer(fcl.authz),
                    fcl.proposer(fcl.authz),
                    fcl.authorizations([fcl.authz]),
                    fcl.limit(9999)
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },
        checkBalance({}, user_addr) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.script`
                        import FungibleToken from 0xAccountFungibleToken
                        import FungibleBeatoken from 0xAccountFungibleBeatoken

                        pub fun main(address: Address) : UFix64 {
                            let vaultPath = FungibleBeatoken.publicReceiverPath

                            let vaultRef = getAccount(address)
                                .getCapability<&FungibleBeatoken.Vault{FungibleToken.Balance}>(vaultPath)
                                .borrow()
                                ?? panic("Could not borrow Balance reference to the Vault")

                            return vaultRef.balance
                        }
                    `,
                    fcl.args([fcl.arg(user_addr, t.Address)]),
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },

        /* For marketplace and manage ntfs */
        allNfts({}, flow_addr) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.script`
                        import NonFungibleToken from 0xAccountNonFungibleToken
                        import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken

                        pub fun main(address: Address) : {UInt64: &NonFungibleBeatoken.NFT?} {

                            let nftRef = getAccount(address).getCapability<&NonFungibleBeatoken.Collection{NonFungibleToken.CollectionPublic, NonFungibleBeatoken.BeatokenCollectionPublic}>(NonFungibleBeatoken.publicReceiver)!
                                .borrow()
                                    ?? panic("Could not borrow acct nft collection reference")

                            var ids = nftRef.getIDs()
                            var nfts : {UInt64: &NonFungibleBeatoken.NFT?} = {}

                            for id in ids {
                                nfts[id] = nftRef.borrowBeatokenNFT(id: id)
                            }

                            return nfts
                        }`,
                        fcl.args([fcl.arg(flow_addr, t.Address)]),
                ]).then(resp => {
                    resolve(fcl.decode(resp))
                }, err => {
                    reject(err.response)
                })
            })
        },
        allNftsForSale({}, flow_addr) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.script`
                    import NonFungibleToken from 0xAccountNonFungibleToken
                    import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken
                    import MarketplaceBeatoken from 0xAccountMarketplaceBeatoken

                    pub fun main(address: Address) : {UInt64: &NonFungibleToken.NFT?} {

                        let acctSaleRef = getAccount(address).getCapability<&AnyResource{MarketplaceBeatoken.SalePublic}>(MarketplaceBeatoken.publicSale)
                            .borrow()
                            ?? panic("Could not borrow acct NFT sale reference")

                        var ids = acctSaleRef.getIDs()
                        var nfts : {UInt64: &NonFungibleToken.NFT?} = {}

                        for id in ids {
                            nfts[id] = acctSaleRef.borrowNFT(id: id)
                        }

                        return nfts
                    }`,
                    fcl.args([fcl.arg(flow_addr, t.Address)])
                ]).then(resp => {
                    resolve(fcl.decode(resp))
                }, err => {
                    reject(err.response)
                })
            })
        },
        getPriceByIds({}, {flow_addr, ids}) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.script`
                    import MarketplaceBeatoken from 0xAccountMarketplaceBeatoken

                    pub fun main(ownerNft: Address, ids: [UInt64]): {UInt64: UFix64?} {
                        let acctSaleRef = getAccount(ownerNft).getCapability<&AnyResource{MarketplaceBeatoken.SalePublic}>(MarketplaceBeatoken.publicSale)
                            .borrow()
                            ?? panic("Could not borrow acct nft sale reference")

                        let prices : {UInt64: UFix64?} = {}

                        for id in ids {
                            prices[id] = acctSaleRef.idPrice(tokenID: id)
                        }

                        return prices
                    }`,
                    fcl.args([
                        fcl.arg(flow_addr, t.Address),
                        fcl.arg(ids, t.Array(t.UInt64))
                    ])
                ]).then(resp => {
                    resolve(fcl.decode(resp))
                }, err => {
                    reject(err.response)
                })
            })
        },
        putNftsForSale({}, dataForSale) {
            return new Promise((resolve, reject) => {
                var ids = [];
                if (dataForSale.flow_id) {
                    ids.push(dataForSale.flow_id)
                } else {
                    ids = dataForSale.ids
                }

                fcl.send([
                    fcl.transaction`
                        import FungibleBeatoken from 0xAccountFungibleBeatoken
                        import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken
                        import MarketplaceBeatoken from 0xAccountMarketplaceBeatoken

                        transaction(ids: [UInt64], price: UFix64) {

                            prepare(acct: AuthAccount) {
                                if acct.borrow<&MarketplaceBeatoken.SaleCollection>(from: MarketplaceBeatoken.storageSale) == nil {

                                    acct.link<&FungibleBeatoken.Vault>(/private/beatokenVault, target: FungibleBeatoken.vaultStoragePath)
                                    let ownerVault = acct.getCapability<&FungibleBeatoken.Vault>(/private/beatokenVault)

                                    acct.link<&NonFungibleBeatoken.Collection>(/private/beatokenNFTCollection, target: NonFungibleBeatoken.storageCollection)
                                    let ownerCollection = acct.getCapability<&NonFungibleBeatoken.Collection>(/private/beatokenNFTCollection)

                                    let sale <- MarketplaceBeatoken.createSaleCollection(
                                        ownerCollection: ownerCollection,
                                        ownerVault: ownerVault
                                    )

                                    acct.save<@MarketplaceBeatoken.SaleCollection>(<-sale, to: MarketplaceBeatoken.storageSale)

                                    acct.link<&MarketplaceBeatoken.SaleCollection{MarketplaceBeatoken.SalePublic}>
                                        (MarketplaceBeatoken.publicSale, target: MarketplaceBeatoken.storageSale)
                                }

                                let collectionRef = acct.borrow<&NonFungibleBeatoken.Collection>(from: NonFungibleBeatoken.storageCollection)
                                    ?? panic("Could not borrow owner's nft collection reference")

                                let sale = acct.borrow<&MarketplaceBeatoken.SaleCollection>(from: MarketplaceBeatoken.storageSale)
                                    ?? panic("Could not borrow from sale in storage")

                                for id in ids {
                                    let token <- collectionRef.withdraw(withdrawID: id)
                                    sale.listForSale(token: <-token, price: price)
                                }
                            }
                        }`,
                    fcl.args([
                        fcl.arg(ids, t.Array(t.UInt64)),
                        fcl.arg(Number(parseFloat(dataForSale.price)).toFixed(8), t.UFix64),
                    ]),
                    fcl.payer(fcl.authz),
                    fcl.proposer(fcl.authz),
                    fcl.authorizations([fcl.authz]),
                    fcl.limit(9999)
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },
        buyTokens({}, dataForBuy) {
            return new Promise((resolve, reject) => {
                var ids = [];
                if (dataForBuy.flow_id) {
                    ids.push(dataForBuy.flow_id)
                } else {
                    ids = dataForBuy.ids
                }

                fcl.send([
                    fcl.transaction`
                    import FungibleToken from 0xAccountFungibleToken
                    import FungibleBeatoken from 0xAccountFungibleBeatoken
                    import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken
                    import MarketplaceBeatoken from 0xAccountMarketplaceBeatoken

                    transaction(middleman: Address, commission: UFix64, ownerNft: Address, ids: [UInt64], price: UFix64) {
                      prepare(acct: AuthAccount) {

                        let vaultRef = acct.borrow<&FungibleBeatoken.Vault>(from: FungibleBeatoken.vaultStoragePath)
                            ?? panic("Could not borrow owner's vault reference")

                        let commissionVault <- vaultRef.withdraw(amount: commission) as! @FungibleBeatoken.Vault

                        let middlemanVault = getAccount(middleman).getCapability(FungibleBeatoken.publicReceiverPath)!
                              .borrow<&FungibleBeatoken.Vault{FungibleToken.Receiver}>()
                              ?? panic("Could not borrow receiver reference to the recipient's Vault")

                        middlemanVault.deposit(from: <-commissionVault)

                        let collectionRef = acct.borrow<&NonFungibleBeatoken.Collection>(from: NonFungibleBeatoken.storageCollection)
                            ?? panic("No nft storage")

                        let saleRef = getAccount(ownerNft).getCapability<&AnyResource{MarketplaceBeatoken.SalePublic}>(MarketplaceBeatoken.publicSale)
                            .borrow()
                            ?? panic("Could not borrow seller's sale reference")

                        for id in ids {
                            let temporaryVault <- vaultRef.withdraw(amount: price) as! @FungibleBeatoken.Vault
                            saleRef.purchase(tokenID: id, recipient: collectionRef, buyTokens: <-temporaryVault)
                        }
                      }
                    }`,
                    fcl.proposer(fcl.authz),
                    fcl.payer(fcl.authz),
                    fcl.authorizations([fcl.authz]),
                    fcl.limit(9999),
                    fcl.args([
                        fcl.arg(dataForBuy.middleman, t.Address),
                        fcl.arg(Number(parseFloat(dataForBuy.commission)).toFixed(8), t.UFix64),
                        fcl.arg(dataForBuy.ownerNft, t.Address),
                        fcl.arg(ids, t.Array(t.UInt64)),
                        fcl.arg(Number(parseFloat(dataForBuy.price)).toFixed(8), t.UFix64)
                    ])
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },
        buyTokenInMarketplace({}, dataForBuy) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.transaction`
                    import FungibleToken from 0xAccountFungibleToken
                    import FungibleBeatoken from 0xAccountFungibleBeatoken
                    import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken
                    import MarketplaceBeatoken from 0xAccountMarketplaceBeatoken

                    transaction(middleman: Address, commission: UFix64, author: Address, commissionAuthor: UFix64, ownerNft: Address, id: UInt64, price: UFix64) {
                      prepare(acct: AuthAccount) {

                        let vaultRef = acct.borrow<&FungibleBeatoken.Vault>(from: FungibleBeatoken.vaultStoragePath)
                            ?? panic("Could not borrow owner's vault reference")

                        let commissionVault <- vaultRef.withdraw(amount: commission) as! @FungibleBeatoken.Vault
                        let commissionAuthorVault <- vaultRef.withdraw(amount: commissionAuthor) as! @FungibleBeatoken.Vault

                        let middlemanVault = getAccount(middleman).getCapability(FungibleBeatoken.publicReceiverPath)!
                              .borrow<&FungibleBeatoken.Vault{FungibleToken.Receiver}>()
                              ?? panic("Could not borrow receiver reference to the recipient's Vault")
                        let authorVault = getAccount(author).getCapability(FungibleBeatoken.publicReceiverPath)!
                              .borrow<&FungibleBeatoken.Vault{FungibleToken.Receiver}>()
                              ?? panic("Could not borrow receiver reference to the recipient's Vault")

                        middlemanVault.deposit(from: <-commissionVault)
                        authorVault.deposit(from: <-commissionAuthorVault)

                        let collectionRef = acct.borrow<&NonFungibleBeatoken.Collection>(from: NonFungibleBeatoken.storageCollection)
                            ?? panic("No nft storage")

                        let saleRef = getAccount(ownerNft).getCapability<&AnyResource{MarketplaceBeatoken.SalePublic}>(MarketplaceBeatoken.publicSale)
                            .borrow()
                            ?? panic("Could not borrow seller's sale reference")

                        let temporaryVault <- vaultRef.withdraw(amount: price) as! @FungibleBeatoken.Vault
                        saleRef.purchase(tokenID: id, recipient: collectionRef, buyTokens: <-temporaryVault)
                      }
                    }`,
                    fcl.proposer(fcl.authz),
                    fcl.payer(fcl.authz),
                    fcl.authorizations([fcl.authz]),
                    fcl.limit(9999),
                    fcl.args([
                        fcl.arg(dataForBuy.middleman, t.Address),
                        fcl.arg(Number(parseFloat(dataForBuy.commission)).toFixed(8), t.UFix64),
                        fcl.arg(dataForBuy.author, t.Address),
                        fcl.arg(Number(parseFloat(dataForBuy.commissionAuthor)).toFixed(8), t.UFix64),
                        fcl.arg(dataForBuy.ownerNft, t.Address),
                        fcl.arg(dataForBuy.flow_id, t.UInt64),
                        fcl.arg(Number(parseFloat(dataForBuy.price)).toFixed(8), t.UFix64)
                    ])
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },
        transferBlucoins({}, {amount, address}) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.transaction`
                    import FungibleToken from 0xAccountFungibleToken
                    import FungibleBeatoken from 0xAccountFungibleBeatoken

                    transaction(commission: UFix64, adminAddress: Address) {
                        var sentVault: @FungibleToken.Vault

                        prepare(acct: AuthAccount) {
                            let vaultRef = acct.borrow<&FungibleBeatoken.Vault>(from: FungibleBeatoken.vaultStoragePath)
                                ?? panic("Could not borrow a reference to the owner's vault")

                            self.sentVault <- vaultRef.withdraw(amount: commission)
                        }

                        execute {
                            let recipient = getAccount(adminAddress)

                            let receiverRef = recipient.getCapability(FungibleBeatoken.publicReceiverPath)!
                                  .borrow<&FungibleBeatoken.Vault{FungibleToken.Receiver}>()
                                  ?? panic("Could not borrow receiver reference to the recipient's Vault")

                            return receiverRef.deposit(from: <-self.sentVault)
                        }
                    }
                `,
                    fcl.proposer(fcl.authz),
                    fcl.payer(fcl.authz),
                    fcl.authorizations([fcl.authz]),
                    fcl.limit(9999),
                    fcl.args([
                        fcl.arg(Number(parseFloat(amount)).toFixed(8), t.UFix64),
                        fcl.arg(address, t.Address),
                    ])
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },
        sendNftAsGift({}, {flowId, recipientAddress}) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.transaction`
                    import NonFungibleToken from 0xAccountNonFungibleToken
                    import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken

                    transaction(flowId: UInt64, recipientAddress: Address) {
                      let nftCollection: &NonFungibleBeatoken.Collection

                      prepare(account: AuthAccount) {
                        self.nftCollection = account.borrow<&NonFungibleBeatoken.Collection>(from: NonFungibleBeatoken.storageCollection)
                            ?? panic("Could not borrow owner's nft collection reference")
                      }

                      execute {
                        let reciever = getAccount(recipientAddress).getCapability(NonFungibleBeatoken.publicReceiver)!
                                    .borrow<&{NonFungibleToken.CollectionPublic}>()
                                        ?? panic("Could not borrow capability from public collection")

                        let token <- self.nftCollection.withdraw(withdrawID: flowId)

                        reciever.deposit(token: <- token)
                      }
                    }`,
                    fcl.proposer(fcl.authz),
                    fcl.payer(fcl.authz),
                    fcl.authorizations([fcl.authz]),
                    fcl.limit(9999),
                    fcl.args([
                        fcl.arg(flowId, t.UInt64),
                        fcl.arg(recipientAddress, t.Address),
                    ])
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },
        removeFromSale({ state }, nft) {
            return new Promise((resolve, reject) => {
                fcl.send([
                    fcl.transaction`
                    import NonFungibleBeatoken from 0xAccountNonFungibleBeatoken
                    import MarketplaceBeatoken from 0xAccountMarketplaceBeatoken

                    transaction(nftId: UInt64) {
                        let market: &MarketplaceBeatoken.SaleCollection
                        let collectionRef: &NonFungibleBeatoken.Collection

                        prepare(acct: AuthAccount) {
                            self.market = acct.borrow<&MarketplaceBeatoken.SaleCollection>(from: MarketplaceBeatoken.storageSale)
                                ?? panic("Could not borrow from sale in storage")

                            self.collectionRef = acct.borrow<&NonFungibleBeatoken.Collection>(from: NonFungibleBeatoken.storageCollection)!
                        }

                        execute {
                            self.market.cancelSale(tokenID: nftId, recipient: self.collectionRef)
                        }
                    }
                `,
                    fcl.proposer(fcl.authz),
                    fcl.payer(fcl.authz),
                    fcl.authorizations([fcl.authz]),
                    fcl.limit(9999),
                    fcl.args([
                        fcl.arg(nft.flow_id, t.UInt64),
                    ])
                ]).then(resp => {
                    resolve(resp)
                }, err => {
                    reject(err.response)
                })
            })
        },
        clearFlowUser({ commit }) {
            commit("setFlowUser", {loggedIn: false})
            fcl.unauthenticate()
        }
    },
    mutations: {
        setFlowUser(state, flowUser) {
            state.flowUser = flowUser
        },
    },
    getters: {
        getFlowUser(state) {
            return state.flowUser
        },
    }
}
