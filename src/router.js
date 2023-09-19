import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from './views/Home.vue';
import Invent from './Components/Invent.vue';
import ImportCSV from './Components/CSV/ImportCSV.vue';
import InventoryTable from './Components/InventoryTable.vue';
import NewAsset from './Components/NewAsset.vue';
import NewCustomField from './Components/NewCustomField.vue';
import AssetDetail from './Components/AssetDetail.vue';
import CreatePDF from './Components/CreatePDF.vue';
import NewPerson from './Components/NewPerson.vue';
import NewRaum from './Components/NewRaum.vue';
import PersonRaum from './Components/PersonRaum.vue';
import { generateUrl } from '@nextcloud/router';
Vue.use(VueRouter);

// The router will try to match routers in a descending order.
// Routes that share the same root, must be listed from the
//  most descriptive to the least descriptive, e.g.
//  /section/component/subcomponent/edit/:id
//  /section/component/subcomponent/new
//  /section/component/subcomponent/:id
//  /section/component/:id
//  /section/:id
const routes = [
	{
		path: '/',
		component: InventoryTable,
	},
	{
		path: '/invent',
		component: Invent,
	},
	{
		path: '/home',
		component: Home,
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
		props: true, // Dynamische Route mit einem Platzhalter ":id"
	},
	{
		path: '/createPDF',
		component: CreatePDF,
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
