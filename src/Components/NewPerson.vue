<template>
	<div class="container">
		<h2>Neue Person</h2>
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
				<tr v-for="person in persons" :key="person.id">
					<td>{{ person.name }}</td>
					<td>
						<button @click="deletePerson(person.id)">Löschen</button>
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
			persons: [],
			form: {
				name: '',
			},
		};
	},
	methods: {
		async submitForm() {
			if (window.confirm(this.form.name + ' wirklich erstellen?')) {
				const response = await axios.post(generateUrl('/apps/itamapp/person'), {
					name: this.form.name,
				});
				this.getPersons(); 
			}
		},
		async getPersons() {
			console.log('getPersons');
			const response = await axios.get(generateUrl('/apps/itamapp/person'));
			console.log(response);
			this.persons = response.data;
		},
		async deletePerson(id) {
			if (window.confirm('Sind Sie sicher, dass Sie diese Person löschen möchten?')) {
				const response = await axios.delete(
					generateUrl(`/apps/itamapp/person/${id}`)
				);
				if (response.status === 200) {
					this.getPersons(); 
				}
			}
		},
	},
	mounted() {
		this.getPersons();
	},
};
</script>

<style scoped>
.container {
	margin-left: 2%;
	margin-top: 2%;
}
</style>
