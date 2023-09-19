import Vue from 'vue'
import Vuex, { Store } from 'vuex'

//import app from './store/app.js'
import assets from './assets.js'

Vue.use(Vuex)

export default new Store({
	modules: {
		//app,
		assets,
	},
})