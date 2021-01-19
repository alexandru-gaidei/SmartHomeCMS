<template>
    <div>
        <video-player  class="video-player-box"
                        ref="videoPlayer"
                        :options="playerOptions">
        </video-player>
        <h3 v-if="!this.inList">{{ this.name }}</h3>
        <div v-if="this.inList">{{ this.name }}</div>
    </div>
</template>

<script>
import 'video.js/dist/video-js.css'
import { videoPlayer } from 'vue-video-player'
export default {
    props: ['url', 'name', 'width', 'height', 'inList'],
    data() {
        return {
            playerOptions: {
                width: this.width,
                height: this.height,
                muted: true,
                language: 'en',
                playbackRates: [0.7, 1.0, 1.5, 2.0],
                sources: [{
                    type: "video/mp4",
                    src: this.url
                }]
            },
            id: this.id
        }
    },
    components: {
        videoPlayer
    },
    computed: {
        player() {
            return this.$refs.videoPlayer.player
        }
    }
}
</script>