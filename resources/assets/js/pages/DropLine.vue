<template>
    <div>
        <main class="drop-full__screen-wrapper count">
            <div class="container">
                <div class="drop-count__container">
                    <div class="drop-count-wrapper" v-if="timeToStartBuy">
                        <div class="drop-count__logo-block">
                            <img src="assets/img/logo.png" class="img-logo" alt="logo" />
                            <img
                                src="assets/img/beatokenTextLogo.svg"
                                class="text-logo"
                                alt="text-logo"
                            />
                        </div>
                        <div class="drop-count__title-block">
                            <h3>YOU ARE NOW IN LINE</h3>
                            <p class="pack-desc">
                                You are in line for {{drop.name}} ({{drop.release_name}})<br> When
                                it is your turn, you will have 20 mintunes to complete the
                                order. When its your turn, it takes up to 1 min to re-direct you
                                to complete the order.
                            </p>
                        </div>
                        <div class="drop-count__count-block">
                            <p class="user-in-line">Number of users in line ahead of you: {{dropLinesCount}}</p>
                            <h2 style="color:white" v-if="displayTime">{{displayTime}}</h2>
                            <p class="status-update">
                                Status last update <span class="update-time"></span>
                                Copenhagen time
                            </p>
                        </div>
                        <div class="live-line__block">
                            <router-link to="/drops" style="cursor:pointer" class="live-line__link">Leave the line</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
<script>
export default {
    name: 'drop-line',
    data() {
        return {
            drop: {},
            dropLine: {},
            dropLinesCount: 0,
            timeToStartBuy: 0,
            displayTime: '',
            timer: null
        }
    },
    beforeCreate() {
        this.$loader(true)
        this.$store.dispatch('drop_lines/myTimeToStartBuyDrop', this.$route.params.drop_id)
            .then((resp) => {
                if (resp.redirectToBuy) this.$router.push('/drop-buy/' + this.$route.params.drop_id)
                if (resp.redirectToDrop) this.$router.push('/drop/' + this.$route.params.drop_id)

                this.dropLine = resp.dropLine
                this.timeToStartBuy = resp.timeToStartBuy
                this.dropLinesCount = resp.dropLinesCount
                this.startTimer()
                setTimeout(() => {this.$loader(false)},1000)
            })
    },
    created() {
        this.getDrop()
    },
    watch: {
        timeToStartBuy(time) {
            if (time === 0) {
                this.stopTimer()
            }
        }
    },
    methods: {
        startTimer() {
            this.timer = setInterval(() => {
                this.timeToStartBuy--

                let minutes = parseInt(this.timeToStartBuy / 60, 10)
                let seconds = parseInt(this.timeToStartBuy % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                this.displayTime = `${minutes}min  ${seconds}sec`;
            }, 1000)
        },
        stopTimer() {
            clearTimeout(this.timer)
            this.$router.push('/drop-buy/' + this.drop.id)
        },
        getDrop() {
            this.$store.dispatch('drops/showDrop', this.$route.params.drop_id)
                .then((drop) => {
                    this.drop = drop
                    if (!drop.nfts.length) this.$router.push('/drop/' + this.$route.params.drop_id)
                }).catch(() => this.$router.push('/drops'))
        },
    }
}
</script>
