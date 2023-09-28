<template>
	<div class="container">
		<h2>Neuer Raum</h2>
		<form @submit.prevent="submitForm">
			<div>
				<label for="name">Name:</label>
				<input type="text" id="name" v-model="form.name" />
			</div>

			<button type="submit" :disabled="form.name === ''">Speichern</button>
		</form>
		<table>
			<thead>
				<tr>
					<th><b>Name</b></th>
					<th><b>Aktion</b></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="raum in raeume" :key="raum.id">
					<td>{{ raum.raumName }}</td>
					<td>
						<button @click="deleteRaum(raum.id)">Löschen</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
export default {
	data() {
		return {
			raeume: [],
			form: {
				name: '',
			},
		};
	},
	methods: {
		async submitForm() {
			if (window.confirm(this.form.name + ' wirklich erstellen?')) {
				const response = await axios.post(generateUrl('/apps/itamapp/raum'), {
					name: this.form.name,
				});
				console.log(response);
				this.getRaueme();
			}
		},
		async getRaueme() {
			console.log('getRaeume');
			const response = await axios.get(generateUrl('/apps/itamapp/raum'));
			this.raeume = response.data;
		},
		async deleteRaum(id) {
			if (window.confirm('Sind Sie sicher, dass Sie diese Person löschen möchten?')) {
				const response = await axios.delete(
					generateUrl(`/apps/itamapp/raum/${id}`)
				);
				if (response.status === 200) {
					this.getRaueme();
				}
			}
		},
	},
	mounted() {
		this.getRaueme();
	},
};
</script>

<style scoped>
.container {
	margin-left: 2%;
	margin-top: 2%;
}
</style>
