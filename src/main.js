import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store/store.js'


__webpack_public_path__ = generateFilePath("itamapp", '', 'js/')

Vue.mixin({ methods: { t, n } })
export default new Vue({
	router,
	store,
	el: '#content',
	render: h => h(App),
})
