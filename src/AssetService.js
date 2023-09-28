import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import { showError } from '@nextcloud/dialogs';

import store from './store/store.js';

function url(url) {
	url = `apps/itamapp${url}`;
	return generateUrl(url);
}

export const fetchAssets = async () => {
	console.log('fetchAssets');
	try {
		const response = await axios.get(generateUrl('/apps/itamapp/assets'));
		//console.log('Hallo Jannes: ' + response.data[0].seriennummer);
		//this.inventar = response.data;
		store.commit('setInventar', response.data);
		//console.log('Inventar ' + store.getters.getInventar);
	} catch (e) {
		console.error(e);
		showError(t('notestutorial', 'Could not fetch assets'));
	}
	try {
		const response = await axios.get(generateUrl('/apps/itamapp/customfields'));
		//TODO: andere Möglichkeit vlt bessere Laufzeit. API call auf assets und customfields zusammen dann group by asset und halt pro zeile ein asset mit einem customfield
		//VLT DB Prof eine Email schicken und fragen wie er es lösen würde
		//this.customFields = response.data[1];
		//console.log("CustomFieldValues: " + JSON.stringify(response.data[1]));
		store.commit('setCustomFieldValues', response.data[1]);
		store.commit('setCustomFields', response.data[0]);
		//response.data[0].forEach((element) => {
		//console.log("CustomField: " + element.id);
		//});
		//this.fields = response.data[0];
		//console.log("Fields: " +this.fields);
	} catch (e) {
		console.error(e);
		showError(t('notestutorial', 'Could not fetch customfields'));
	}
	try {
		const response = await axios.get(generateUrl('/apps/itamapp/raum'));
		store.commit('setRaum', response.data);
		//console.log("Raum: " + store.getters.getRaum[1].id);
	} catch (e) {
		console.error(e);
		showError(t('notestutorial', 'Could not fetch raum'));
	}
	try {
		const response = await axios.get(generateUrl('/apps/itamapp/person'));
		store.commit('setPersonen', response.data);
		//console.log('Personen: ' + store.getters.getPersonen);
	} catch (e) {
		console.error(e);
	}
};
export async function postAsset(asset) {
	try {
		const {
			inventarnummer,
			rechnungsdatum,
			seriennummer,
			locationId,
			personId,
			customFieldValues,
		} = asset;

		const response = await axios.post(generateUrl('/apps/itamapp/assets'), {
			inventarnummer,
			rechnungsdatum,
			seriennummer,
			locationId,
			personId,
			customFieldValues,
		});

		return response.data;
	} catch (error) {
		throw error;
	}
}
export async function postAssets(assets) {
	try {
		const response = await axios.post(generateUrl('/apps/itamapp/csv'), assets);
		return response.data;
	} catch (error) {
		throw error;
	}
}
export async function editAsset(asset) {
	try {
		let {
			id,
			inventarnummer,
			rechnungsdatum,
			seriennummer,
			locationId,
			personId,
			customFieldValues,
		} = asset;
		if (asset.locationId === 'null') {
			locationId = null;
		} else if (asset.personId === 'null') {
			personId = null;
		}

		const response = await axios.put(generateUrl('/apps/itamapp/assets/' + id), {
			id,
			inventarnummer,
			rechnungsdatum,
			seriennummer,
			locationId,
			personId,
			customFieldValues,
		});

		return response.data;
	} catch (error) {
		console.error(error);
	}
}

export async function deleteAsset(id) {
	try {
		const response = await axios.delete(generateUrl('/apps/itamapp/assets/' + id));
		return response.data;
	} catch (error) {
		console.error(error);
	}
}
