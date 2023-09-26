<template>
	<div class="container">
		<h2 style="margin-left: 50px">Person Raum Wechsel</h2>
		<div class="field" v-if="personRaum" v-for="person in personen" :key="person.id">
			<span class="label">{{ person.name }}:</span>

			<select class="input" v-model="selectedRaumIds[person.id]">
				<option disabled value="">Bitte einen Raum auswählen</option>
				<option value="null">Kein Raum</option>
				<option v-for="r in raeume" :value="r.id" :key="r.id">
					{{ r.raumName }}
				</option>
			</select>

			<button @click="updatePersonRaum(person.id)">Ändern</button>
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import { fetchAssets } from '../AssetService';


export default {
	data() {
		return {
			personRaum: null,
			selectedRaumIds: {},
		};
	},
	computed: {
		personName() {
			const person = this.personen.find((p) => p.id === this.hauptObjekt.personId);
			return person ? person.name : 'Unbekannt';
		},
		raumName() {
			const raum = this.raeume.find((r) => r.id === this.hauptObjekt.raumId);
			return raum ? raum.name : 'Unbekannt';
		},
		raeume() {
			return this.$store.getters.getRaum;
		},
		personen() {
			return this.$store.getters.getPersonen;
		},
	},
	methods: {
		async getPersonRaum() {
			const response = await axios.get(generateUrl('/apps/itamapp/person'));
			this.personRaum = response.data;
			console.log('Got ' + JSON.stringify(this.personRaum));
			console.log(this.raeume);
			this.personRaum.forEach((rp) => {
				this.selectedRaumIds[rp.id] = rp.locationId;
			});
			console.log(JSON.stringify(this.selectedRaumIds));
		},
		async updatePersonRaum(personId) {
			let raumId = this.selectedRaumIds[personId];
			console.log('Update ' + personId + ' to ' + raumId);
			if (raumId === 'null') {
				raumId = null;
			}
			const response = await axios.put(generateUrl('/apps/itamapp/person/'+personId), {
				locationId: raumId,
			});
		},
		getRaumIdForPerson(personId) {
			const mapping = this.personRaum.find((rp) => rp.personId === personId);
			return mapping ? mapping.raumId : '';
		},
	},
	async created() {
		await fetchAssets();
		await this.getPersonRaum();
		this.personRaum.forEach((rp) => {
			this.selectedRaumIds[rp.personId] = rp.raumId;
		});
	},
};
</script>

<style scoped>
.field {
	display: flex;
	align-items: center;
	justify-content: space-between;
	margin-bottom: 10px;
}
.input {
	width: 65%;
	margin-left: 20px;
}
.label {
	width: 30%;
	text-align: right;
	margin-right: 10px;
}
.container {
	max-width: 800px;
	margin: left auto;
}
</style>
