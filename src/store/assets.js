const state = {
	defaultFields: [],
	customFieldValues: [],
	customFields: [],
	inventar: [],
	raum: {},
	personen: [],
	defaultAssetFields: ['Inventarnummer', 'Rechnungsdatum', 'Seriennummer', 'Raum', 'Person'],
};

const getters = {
	getInventar: (state) => state.inventar,
	getInventarById: (state) => (id) => {
		return state.inventar.find((inventar) => inventar.id === id);
	},
	getCustomFields: (state) => state.customFields,
	getCustomFieldValues: (state) => state.customFieldValues,
	getCustomFieldValuesById: (state) => (id) => {
		return state.customFieldValues.filter(
			(customFieldValues) => customFieldValues.assetId === id
		);
	},
	getRaum: (state) => state.raum,
	getPersonen: (state) => state.personen,
	getDefaultAssetFields: (state) => state.defaultAssetFields,
};
const mutations = {
	setInventar(state, inventar) {
		state.inventar = inventar;
	},
	setCustomFields(state, customFields) {
		state.customFields = customFields;
	},
	setCustomFieldValues(state, customFieldValues) {
		state.customFieldValues = customFieldValues;
	},
	setRaum(state, raum) {
		state.raum = raum;
	},
	setPersonen(state, personen) {
		state.personen = personen;
	},
};

export default { state, getters, mutations };
