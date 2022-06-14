<template>
    <div>
        <h3 class="admin-table__title">Categories</h3>
        <div class="categories-wrapper">
            <div class="accordion-block">
                <div class="accordion" id="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseOne"
                                aria-expanded="true"
                                aria-controls="collapseOne">
                                Create set
                            </button>
                        </h2>
                        <div
                            id="collapseOne"
                            class="accordion-collapse collapse show"
                            aria-labelledby="headingOne"
                            data-bs-parent="#accordion">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label for="formControlInput1" class="form-label">Set name *</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="formControlInput1"
                                        placeholder="Enter set name"
                                        v-model="set.name"
                                    />
                                </div>
                                <button class="submit" @click="addSet">Add set</button>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item" v-if="sets.length">
                        <h2 class="accordion-header" id="headingTwo">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo"
                                aria-expanded="false"
                                aria-controls="collapseTwo">
                                Create release
                            </button>
                        </h2>
                        <div
                            id="collapseTwo"
                            class="accordion-collapse collapse"
                            aria-labelledby="headingTwo"
                            data-bs-parent="#accordion">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label for="formControlInput2" class="form-label">Release name *</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="formControlInput2"
                                        placeholder="Enter release name"
                                        v-model="release.name"
                                    />
                                </div>
                                <div class="mb-3 select-set__block">
                                    <label class="form-label">Parent set *</label>
                                    <select
                                        class="select"
                                        v-model="release.set_id">
                                        <option selected value="0">Select set</option>
                                        <option v-for="(set, i) in sets" :value="set.id">{{ set.name }}</option>
                                    </select>
                                </div>

                                <button class="submit" @click="addRelease">Add release</button>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item" v-if="sets.length && releases.length">
                        <h2 class="accordion-header" id="headingThree">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseThree"
                                aria-expanded="false"
                                aria-controls="collapseThree">
                                Create pack
                            </button>
                        </h2>
                        <div
                            id="collapseThree"
                            class="accordion-collapse collapse"
                            aria-labelledby="headingThree"
                            data-bs-parent="#accordion">
                            <div class="accordion-body">
                                <div class="mb-3 select-set__block">
                                    <label for="formControlInput3" class="form-label">Pack name *</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="formControlInput3"
                                        placeholder="Enter pack name"
                                        v-model="pack.name"
                                    />
                                </div>
                                <div class="flex-center">
                                    <div>
                                        <label for="nft_img" class="label-photo btn btn-success">Select file for pack *</label>
                                        <label>{{pack.file.name}}</label>
                                        <input style="display:none" type="file" @change="processFileCreate($event)" id="nft_img" accept="image/jpeg,image/png">
                                    </div>
                                </div>
                                <div class="mb-3 select-set__block">
                                    <label for="formControlInput4" class="form-label">Pack price</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="formControlInput4"
                                        placeholder="Enter pack price"
                                        step="0.01"
                                        min="0.01"
                                        max="9999.99"
                                        v-model="pack.price"
                                    />
                                </div>
                                <div class="mb-3 select-set__block">
                                    <label class="form-label">Parent release *</label>
                                    <select
                                        class="select"
                                        v-model="pack.release_id">
                                        <option selected value="0">Select release</option>
                                        <option v-for="(release, i) in releases" :value="release.id">{{ '('+ release.set.name +') - ' + release.name }}</option>
                                    </select>
                                </div>

                                <button class="submit" @click="addPack">Add pack</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="categories-table__block">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-if="categories.length" v-for="(category, i) in categories">
                            <td>
                                <p>{{category.name}}</p>
                            </td>
                            <td>{{category.description ? category.description : 'No description'}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Categories',
    data() {
        return {
            categories: [],
            sets: [],
            releases: [],
            packs: [],

            set: {
                name: ''
            },
            release: {
                name: '',
                set_id: 0
            },
            pack: {
                name: '',
                release_id: 0,
                price: 0.01,
                file: {}
            }
        }
    },
    created() {
        this.getCategories()
    },
    methods: {
        processFileCreate(e) {
            this.pack.file = e.target.files[0]
        },
        addSet() {
            if (!this.set.name) return false

            this.$store.dispatch('categories/addSet', this.set)
                .then(() => {
                    this.set = {name: ''}
                    this.getCategories()
                })
        },
        addRelease() {
            if (!this.release.name || !this.release.set_id) {this.release = {name: '', set_id: 0}; return false}

            this.$store.dispatch('categories/addRelease', this.release)
                .then(() => {
                    this.release = {name: '', set_id: 0}
                    this.getCategories()
                })
        },
        addPack() {
            if (!this.pack.name || !this.pack.release_id || !this.pack.file) {this.pack = {name: '', release_id: 0, price: null, file: {}}; return false}

            const formData = new FormData()
            if (this.pack.name) formData.append('name', this.pack.name)
            if (this.pack.release_id) formData.append('release_id', this.pack.release_id)
            if (this.pack.price) formData.append('price', this.pack.price)
            if (this.pack.file) formData.append('file', this.pack.file)

            this.$store.dispatch('categories/addPack', formData)
                .then(() => {
                    this.pack = {name: '', release_id: 0, price: null, file: {}}
                    this.getCategories()
                })
        },
        getCategories() {
            this.$store.dispatch('categories/getCategories')
                .then((res) => {
                    this.categories = res.categories

                    this.sets = res.sets
                    this.releases = res.releases
                    this.packs = res.packs
                })
        }
    }
}
</script>
