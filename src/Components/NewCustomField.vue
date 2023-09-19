<template>
	<div class="container">
		<h2>Neues Custom-Feld</h2>
		<form @submit.prevent="submitForm">
			<div>
				<label for="name"><b>Name:</b></label>
				<input type="text" id="name" v-model="form.name" />
			</div>
			<div>
				<label for="type"><b>Typ:</b></label>
				<select id="type" v-model="form.type">
					<option value="string">String</option>
					<option value="number">Number</option>
					<option value="date">Date</option>
				</select>
			</div>
			<button type="submit" :disabled="form.name === ''">Speichern</button>
		</form>
	</div>
</template>

<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
export default {
	data() {
		return {
			form: {
				name: '',
				type: 'string',
			},
		};
	},
	methods: {
		async submitForm() {
			if (window.confirm(this.form.name + ' wirklich erstellen?')) {
				console.log(this.form);
				// Hier können Sie dann Ihre Logik zum Speichern der Daten einfügen
				const response = await axios.post(
					generateUrl('/apps/itamapp/customfields'),
					{
						name: this.form.name,
						type: this.form.type,
					}
				);
				console.log(response);
			}
		},
	},
};
</script>

<style scoped>
.container {
	margin-left: 2%;
	margin-top: 2%;
}
</style>
