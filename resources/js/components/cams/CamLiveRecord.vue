<template>
    <div>
        <small><a href="#" @click.prevent="showFullScreen()" :hidden="showFullscreen">Show fullscreen</a></small>
        <div id="videoWrapper" :class="{ fullScreen: showFullscreen }">
            <a href="#" class="close-fs" :hidden="!showFullscreen" @click.prevent="showFullScreen()">close</a>
            <canvas id="canvas"></canvas>
        </div>
    </div>
</template>

<script>
import JSMpeg from '@cycjimmy/jsmpeg-player';
import client from '../../client';

export default {
    props: ['cam'],
    data() {
        return {
            player: null,
            showFullscreen: false,
        }
    },
    methods: {
        showFullScreen() {
            this.showFullscreen = !this.showFullscreen;
        },
    },
    mounted() {
        new JSMpeg.VideoElement('#videoWrapper', `ws://${document.location.hostname}:${9050 + parseInt(this.cam.id)}/`, {
            canvas: 'canvas'
        });
    }
}
</script>

<style scoped>
#videoWrapper {
    width: 320px;
    height: 240px;
}
#canvas {
    position: absolute !important;
    width: 100%;
}
.fullScreen #canvas {
    position: relative !important;
}
.fullScreen {
    position: fixed !important;
    z-index: 10000;
    width: auto !important;
    height: auto !important;
    display: inline-block;
    left: 50%;
    margin-left: -640px;
    top: 50%;
    margin-top: -360px;
}
.close-fs {
    position: relative;
    z-index: 100000;
    background: rgba(255, 255, 255, 0.6)
}
</style>