import Vue from "vue";
import Vuex, { Store } from "vuex";

import assets from "./assets.js";

Vue.use(Vuex);

export default new Store({
    modules: {
        assets,
    },
});
