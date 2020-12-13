<template>
  <div id="carousel-component">
    <b-carousel id="carousel" :interval="4000" indicators controls>
      <b-carousel-slide v-for="(image, index) in images" :key="index">
        <template #img>
          <img
            class="d-block img-fluid carousel-img"
            width="100%"
            height="100%"
            :src="image"
          />
        </template>
      </b-carousel-slide>
    </b-carousel>
  </div>
</template>

<script>
import { CarouselPlugin } from "bootstrap-vue";

Vue.use(CarouselPlugin);

export default {
  props: {
    images: {
      type: Array,
      default: [],
    },
  },
};
</script>

<style scoped>
#carousel-component {
  width: 100%;
  height: 200px;
  border-radius: 20px;
}
.carousel-img {
  object-fit: cover;
  border-radius: 20px;
  height: 200px;
  width: 100%;
}
</style>