<template>
	<div v-if="assetLoaded" class="detailContent">
		<h2 style="margin-left: 50px">Asset Detail</h2>

		<div class="container">
			<form @submit.prevent="speichern">

				<div class="field">
					<label class="label">Inventarnummer</label>
					<input class="input" v-model="inventarnummer" type="text" disabled />
				</div>

				<div class="field">
					<label class="label">Rechnungsdatum:</label>
					<input class="input" v-model="rechnungsdatum" type="date" />
				</div>

				<div class="field">
					<label class="label">Seriennummer:</label>
					<input class="input" v-model="seriennummer" type="text" />
				</div>

				<div class="field">
					<label class="label" for="raum">Raum:</label>
					<select class="input" id="raum" v-model="raum">
						<option value="null">Kein Raum</option>
						<option v-for="r in raeume" :value="r.id" :key="r.id">
							{{ r.raumName }}
						</option>
					</select>
				</div>

				<div class="field">
					<label class="label">Person:</label>
					<select class="input" id="person" v-model="person">
						<option value="null">Keine Person</option>
						<option v-for="p in personen" :value="p.id" :key="p.id">
							{{ p.name }}
						</option>
					</select>
				</div>

				<div class="field" v-for="(field, index) in customFields" :key="index">
					<label class="label"> {{ field.name }}: </label>
					<input
						class="input"
						:type="field.type === 'int' ? 'text' : field.type"
						v-model="customFieldValues[field.id]" />
				</div>
				<button type="submit">Speichern</button>
			</form>
		</div>

		<button @click="deleteThis()">Löschen</button>
		<button @click="generateQRCode()">QR-Code</button>
		<qrcode-vue v-if="qrValue" :value="qrValue" ref="qrCode"></qrcode-vue>
	</div>
</template>

<script>
import { editAsset, deleteAsset, fetchAssets } from '../AssetService';
import QrcodeVue from 'qrcode.vue';

export default {
	name: 'AssetDetail',
	data() {
		return {
			inventarnummer: '',
			rechnungsdatum: '',
			seriennummer: '',
			raum: '',
			person: '',
			customFieldValues: {},
			loading: true,
			qrValue: '',
		};
	},
	components: {
		QrcodeVue,
	},
	methods: {
		async generateQRCode() {
			//setze QR-Code Value auf aktuelle URL
			this.qrValue = window.location.href;

			await this.$nextTick();

			const qrElement = this.$refs.qrCode.$el.querySelector('canvas');
			//Erstelle Link zum Download und lade QR-Code herunter
			if (qrElement) {
				const link = document.createElement('a');
				link.download = this.inventarnummer+'_QRCode.png';
				link.href = qrElement.toDataURL('image/png');
				link.click();
			}
		},
		async speichern() {
			if (window.confirm('Änderungen speichern?')) {
				try {
					let id = await editAsset({
						id: Number(this.$route.params.id),
						inventarnummer: this.inventarnummer,
						rechnungsdatum: this.rechnungsdatum,
						seriennummer: this.seriennummer,
						locationId: this.raum,
						personId: this.person,
						customFieldValues: this.customFieldValues,
					});
					alert('Asset wurde bearbeitet');
					this.$router.push('/');
				} catch (error) {
					console.log('Error: ', error);
				}
			}
			console.log('ID: ', id);
			//fetchAssets();
		},
		async deleteThis() {
			
			if (window.confirm('Dieses Asset wirklich löschen?')) {
				try {
					let id = await deleteAsset(Number(this.$route.params.id));
					alert('Asset wurde gelöscht');
					this.$router.push('/');
				} catch (error) {
					console.log('Error: ', error);
				}
			}

		},
	},

	computed: {
		customFields() {
			return this.$store.getters.getCustomFields;
		},
		customFieldValuesFetch() {
			return this.$store.getters.getCustomFieldValuesById(Number(this.$route.params.id));
		},
		raeume() {
			return this.$store.getters.getRaum;
		},
		inventar() {
			return this.$store.getters.getInventarById(Number(this.$route.params.id));
		},
		personen() {
			return this.$store.getters.getPersonen;
		},
		assetLoaded() {
			return (
				this.raeume !== null && this.customFields !== null && this.personen !== null && this.inventar !== null
			);
		},
	},
	beforeMount() {
		this.inventarnummer = this.inventar.inventarnummer;
		this.rechnungsdatum = this.inventar.rechnungsdatum;
		this.seriennummer = this.inventar.seriennummer;
		this.raum = this.inventar.locationId;
		this.person = this.inventar.personId;
		this.customFields.forEach((field) => {
			let value = this.customFieldValuesFetch.find((value) => value.name === field.name);
			this.customFieldValues[field.id] = value ? value.value : '';
		});
		this.loading = false;
	},
	mounted() {
		this.$nextTick(() => {
			console.log('Initialisierte Daten: ', this.seriennummer);
		});
		console.log('Seiten URL ' + window.location.href);
	},
	watch: {
		inventar: {
			handler(newVal, oldVal) {
				//console.log("Inventar: ", JSON.stringify(newVal));
				this.inventarnummer = newVal.inventarnummer;
				this.rechnungsdatum = newVal.rechnungsdatum;
				this.seriennummer = newVal.seriennummer;
				this.raum = newVal.locationId;
				this.person = newVal.personId;
				this.loading = false;
			},
			deep: true,
		},
		raeume(newVal, oldVal) {
			console.log('Räume: ', JSON.stringify(newVal));
		},
		customFieldValuesFetch(newVal, oldVal) {
			this.customFields.forEach((field) => {
				let value = newVal.find((value) => value.name === field.name);
				this.customFieldValues[field.id] = value ? value.value : '';
			});
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
