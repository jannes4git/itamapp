<template>
	<div class="detailContent">
		<h2 style="margin-left: 50px">Neues Asset erstellen</h2>
		<div class="container">
			<form @submit.prevent="submitForm">
				<div class="field">
					<label class="label"> Rechnungsdatum: </label>
					<input class="input" type="date" v-model="rechnungsdatum" required />
				</div>
				<div class="field">
					<label class="label"> Seriennummer: </label>
					<input class="input" type="text" v-model="seriennummer" />
				</div>
				<div class="field">
					<label class="label"> Raum: </label>

					<select class="input" v-model="raum">
						<option disabled value="">Raum wählen</option>
						<option v-for="raum in raeume" :key="raum.id" :value="raum">
							{{ raum.raumName }}
						</option>
					</select>
				</div>
				<div class="field">
					<label class="label"> Person: </label>

					<select class="input" v-model="person">
						<option disabled value="">Person wählen</option>
						<option v-for="person in personen" :key="raum.id" :value="person">
							{{ person.name }}
						</option>
					</select>
				</div>

				<div class="field" v-for="(field, index) in customFields" :key="index">
					<label class="label"> {{ field.name }}: </label>
					<input class="input" type="text" v-model="customFieldValues[field.name]" />
				</div>
				<button type="submit" :disabled="rechnungsdatum===''">Erstellen</button>

			</form>
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import { postAsset } from '../AssetService';

export default {
	data() {
		return {
			rechnungsdatum: '',
			seriennummer: '',
			raum: '',
			person: '',
			customFieldValues: {},
		};
	},
	mounted() {
		console.log('MOunt ' + this.raeume);
	},
	computed: {
		customFields() {
			return this.$store.getters.getCustomFields;
		},
		raeume() {
			return this.$store.getters.getRaum;
		},
		personen() {
			return this.$store.getters.getPersonen;
		},
	},
	methods: {
		fieldValue(fieldName) {
			return this[fieldName.toLowerCase()];
		},
		async submitForm(){
			if(this.rechnungsdatum == '' || this.rechnungsdatum == null){
				alert('Bitte Rechnungsdatum angeben');
				return;
			}
			console.log('Create Asset' + this.raum.id + ' ' + this.person.id);
			const asset = {
				rechnungsdatum: this.rechnungsdatum,
				seriennummer: this.seriennummer,
				locationId: this.raum.id,
				personId: this.person.id,
				customFieldValues: this.customFieldValues,
			};
			console.log('Asset: ', asset);
			try {
				let response = await postAsset(asset);
				console.log('Response: ', response.status);
				alert('Asset erfolgreich erstellt' + JSON.stringify(response));
				this.$router.push('/');
			} catch (error) {
				console.log('Error: ', error);
			}
		},
		async createAsset() {
			if(this.rechnungsdatum == '' || this.rechnungsdatum == null){
				alert('Bitte Rechnungsdatum angeben');
				return;
			}
			console.log('Create Asset' + this.raum.id + ' ' + this.person.id);
			const asset = {
				rechnungsdatum: this.rechnungsdatum,
				seriennummer: this.seriennummer,
				locationId: this.raum.id,
				personId: this.person.id,
				customFieldValues: this.customFieldValues,
			};
			console.log('Asset: ', asset);
			try {
				let response = await postAsset(asset);
				console.log('Response: ', response.status);
				alert('Asset erfolgreich erstellt' + JSON.stringify(response));
				this.$router.push('/');
			} catch (error) {
				console.log('Error: ', error);
			}
			
		},
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
.detailContent {
	margin-left: px;
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

.input {
	width: 65%;
	margin-left: 20px;
}
</style>
