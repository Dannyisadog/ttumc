import carousel from './components/carousel.vue';

var homeVue = new Vue({
    el: '#home-page',
    data() {
        return {
            carouselImages: [
                '/images/banner/banner1.jpg',
                '/images/banner/banner2.jpg',
                '/images/banner/banner3.jpg',
                '/images/banner/banner4.jpg',
            ]
        }
    },
    components: {
        'carousel': carousel
    }
});