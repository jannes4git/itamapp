import Vue from 'vue';
import VueRouter from 'vue-router';
import ImportCSV from './Components/CSV/ImportCSV.vue';
import AssetTable from './Components/AssetTable.vue';
import NewAsset from './Components/NewAsset.vue';
import NewCustomField from './Components/NewCustomField.vue';
import AssetDetail from './Components/AssetDetail.vue';
import NewPerson from './Components/NewPerson.vue';
import NewRaum from './Components/NewRaum.vue';
import PersonRaum from './Components/PersonRaum.vue';
import { generateUrl } from '@nextcloud/router';
Vue.use(VueRouter);

const routes = [
	{
		path: '/',
		component: AssetTable,
	},
	{
		path: '/importCSV',
		component: ImportCSV,
	},
	{
		path: '/newAsset',
		component: NewAsset,
	},
	{
		path: '/newPerson',
		component: NewPerson,
	},
	{
		path: '/newRaum',
		component: NewRaum,
	},
	{
		path: '/newCustomField',
		component: NewCustomField,
	},
	{
		path: '/asset/:id',
		component: AssetDetail,
		name: 'AssetDetail',
		props: true,
	},
	{
		path: '/personRaum',
		component: PersonRaum,
	},
];

export default new VueRouter({
	//mode: "history", //warum gehts mit history nicht url einfügen in browser?
	base: generateUrl('apps/itamapp'),
	linkActiveClass: 'active',
	routes,
});
