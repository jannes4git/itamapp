<template>
	<div style="height: 80vh; overflow-y: auto" class="csvContent">
		<h2 style="margin-left: 30px">CSV Import</h2>
		<div>
			<input type="file" @change="handleFileUpload" accept=".csv" />
		</div>

		<div v-if="csvData" class="container">
			<div class="headers">
				<h3>Asset Felder</h3>
				<h3>CSV Felder</h3>
			</div>
			<div v-for="(feld, index) in dbFields" :key="index" class="fields">
				<p>{{ feld }}</p>
				<select v-model="selected[index]" @change="handleChange()">
					<option disabled value="">Nicht zugeordnet</option>
					<option value="none">Nicht zuordnen</option>
					<option
						v-for="(csvColumn, i) in availableCsvColumns(index)"
						:key="i"
						:value="csvColumn"
						:disabled="isDisabled(csvColumn)">
						{{ csvColumn }}
					</option>
				</select>
			</div>
			<button @click="importCSV">Import</button>
			<div>
				<NcPopover :focus-trap="false">
					<template #trigger>
						<NcButton>?</NcButton>
					</template>
					<template>
						<p>Entweder Rechnungsdatum oder Inventarnummer sollte zugeordnet werden. 
							<br>Andernfalls kann keine Inventarnummer eingefügt oder erstellt werden. <br>
							Falls nur das Rechnungsdatum zugeordnet wird und dieses in der CSV nicht in jeder Zeile vorhanden ist, wird der Import abgebrochen.</p>

					</template>
				</NcPopover>
			</div>
			
		</div>

	</div>
</template>

<script>
import Papa from 'papaparse';
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import fuzzysort from 'fuzzysort';
import { postAsset, postAssets } from '../../AssetService';
import NcPopover from '@nextcloud/vue/dist/Components/NcPopover.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

export default {
	components: {
		NcPopover,
		NcButton,
	},
	props: {
		databaseFields: {
			type: Array,
			required: true,
		},
	},
	async mounted() {
		await this.getColumns();
	},
	data() {
		return {
			csvData: null,
			dbFields: [],
			defaultFelder: ['Inventarnummer', 'Rechnungsdatum', 'Seriennummer'],
			customFelder: [],
			csvFields: [],
			/**
			 * Array mit den zugeordneten CSV Feldern: dbArray ist der index und der Wert ist das zugeordnete CSV Feld
			 */
			selected: [],
			dbInfo: {},
			daten: null,
		};
	},
	created() {},
	methods: {
		select() {
			console.log(t);
			this.selected = Array(this.dbFields.length).fill('');
		},
		/**
		 * Importiert die zugeordneten CSV Felder in die Datenbank
		 */
		async importCSV() {
			console.log('Import CSV');
			if(!window.confirm('Wirklich importieren?')){
				return;
			}
			//Default-Feld-Zuordnungen:
			var inventarnummer = this.selected[this.dbFields.indexOf('Inventarnummer')];
			var rechnungsdatum = this.selected[this.dbFields.indexOf('Rechnungsdatum')];
			var seriennummer = this.selected[this.dbFields.indexOf('Seriennummer')];
			let allAssets = [];
			//Gehe alle Zeilen der CSV durch
			for (let row of this.csvData) {
				var asset = {};
				//Schreibe Default-Felder aus der CSV Zuordnung in asset
				asset = {
					inventarnummer: row[inventarnummer],
					rechnungsdatum: row[rechnungsdatum],
					seriennummer: row[seriennummer],
					customFieldValues: {},
				};
				//Schreibe CustomFieldValues aus der CSV Zuordnung in asset
				for (let i = 3; i < this.dbFields.length; i++) {
					asset.customFieldValues[this.dbFields[i]] = row[this.selected[i]];
				}
				//Füge das Asset dem Asset-Array hinzu falls es valide Werte enthält
				if (this.hasValidValue(asset)) {
					allAssets.push(asset);
				}
			}
			try{
				let response = await postAssets(allAssets);
				console.log('Response: ', response.status);
				alert('Import von ' + JSON.stringify(response)+ ' Assets erfolgreich');
				this.$router.push('/');
			} catch (error) {
				alert('Import fehlgeschlagen: ' + error.response.data.message + '\nBitte überprüfen Sie die CSV Datei in den Zeilen:\n'+error.response.data.zeilen);
			}
			//await this.postCSV(allAssets);
			console.log('Import fertig');
		},
		/**
		 * Check ob ein Asset gültige Werte hat
		 * @param {*} asset 
		 */
		hasValidValue(asset) {
			for (let key in asset) {
				if (asset.hasOwnProperty(key)) {
					let value = asset[key];

					// Wenn der Wert ein String ist und nicht leer
					if (typeof value === 'string' && value.trim() !== '') {
						return true;
					}

					// Wenn der Wert ein Objekt ist, überprüfen Sie, ob es nicht leer ist und ob seine Werte nicht leer sind
					if (typeof value === 'object') {
						for (let subKey in value) {
							if (
								value.hasOwnProperty(subKey) &&
								value[subKey] &&
								value[subKey].trim() !== ''
							) {
								return true;
							}
						}
					}
				}
			}
			return false;
		},
		async postCSV(csvData) {
			let response = await postAssets(csvData);
			console.log(response);
		},
		/**
		 * Check ob ein CSV Feld bereits zugeordnet wurde
		 * @param {*} csvColumn 
		 */
		isDisabled(csvColumn) {
			return this.selected.includes(csvColumn);
		},
		/**
		 * Liest die CSV Datei ein, speichert die Felder in csvFields und die Daten als Objekte in csvData.
		 * Führt dann eine automatische Zuordnung durch.
		 * 
		 * @param {*} event 
		 */
		handleFileUpload(event) {
			const file = event.target.files[0];

			Papa.parse(file, {
				complete: (results) => {
					// `results.data` ist ein Array von Zeilen. Jede Zeile ist ein Array von Feldern.
					this.csvFields = results.data[0];
					this.csvData = results.data.slice(1).map((row) => {
						const obj = {};
						this.csvFields.forEach((field, index) => {
							obj[field] = row[index];
						});
						return obj;
					});
					this.autoMapColumns();
				},
			});
		},
		autoMapColumns() {
			this.dbFields.forEach((dbColumn, index) => {
				console.log(dbColumn, index);
				const dbColumnSanitized = dbColumn.toLowerCase().replace(/\s+/g, '');
				const matchingCsvColumn = this.csvFields.find(
					(csvColumn) => csvColumn.toLowerCase().replace(/\s+/g, '') === dbColumnSanitized
				);
				if (matchingCsvColumn) {
					this.selected[index] = matchingCsvColumn;
				} else {
					const bestMatch = fuzzysort.go(
						dbColumnSanitized,
						this.csvFields.map((csvColumn) =>
							csvColumn.toLowerCase().replace(/\s+/g, '')
						),
						{ limit: 1 }
					)[0];
					if (bestMatch && bestMatch.score > -10000) {
						// Sie können den Schwellenwert anpassen, um die Sensibilität des Fuzzy-Matching zu steuern
						this.selected[index] = this.csvFields[bestMatch.index];
					}
				}
			});
		},
		/**
		 * Gibt das Mapping von Datenbankfeldern zu CSV Feldern zurück
		 */
		getMapping() {
			let mapping = {};
			for (let i = 0; i < this.dbFields.length; i++) {
				mapping[this.dbFields[i]] = this.selected[i];
			}
			for (let type in this.dbInfo) {
				let table = type === 'default' ? 'default_table' : 'custom_table';
				console.log(table);
			}
			return mapping;
		},
		availableCsvColumns(index) {
			const selectedColumns = this.selected.filter(
				(selected, selectedIndex) => selectedIndex !== index && selected !== 'none'
			);
			return this.csvFields.filter((csvColumn) => !selectedColumns.includes(csvColumn));
		},
		handleChange() {
			//console.log('handleChange', index);
			//this.$set(this.selected, index, this.selected[index]);
			console.log(this.selected);
		},
		async getColumns() {
			this.daten = {
				Inventarnummer: {
					field: 'inventarnummer',
					type: 'default',
					table: 'default_table',
				},
				Seriennummer: {
					field: 'seriennummer',
					type: 'default',
					table: 'default_table',
				},
				Raum: { field: 'raum', type: 'default', table: 'default_table' },
				Rechnungsdatum: {
					field: 'rechnungsdatum',
					type: 'default',
					table: 'default_table',
				},
			};
			//const columns = (await axios.get(generateUrl("/apps/itamapp/meta"))).data;
			var columnsDB = (await axios.get(generateUrl('/apps/itamapp/meta'))).data;
			//console.log(this.dbColumns[0][0].COLUMN_NAME);
			//TODO: Defaultfelder vielleicht hardcoden und nicht fetchen? -> ja machen!
			//this.defaultFelder = columnsDB[0];
			this.customFelder = columnsDB[1];

			this.defaultFelder.forEach((element) => {
				console.log('Pushe: ', element);
				this.dbFields.push(element);
			});
			this.customFelder.forEach((element) => {
				this.dbFields.push(element.name);
				this.daten[element.name] = {
					field: element.name,
					type: 'custom',
					table: 'custom_table',
				};
			});
			this.select(); 
		},
	},
};
</script>

<style scoped>
.fields,
.headers {
	display: flex;
	align-items: center;
	justify-content: start;
	gap: 10px;
}
.headers h3,
.fields p {
	flex: 1;
	margin: 0;
}

.fields select {
	flex: 2;
}
.container {
	max-width: 800px;
	margin: left auto;
}
.csvContent {
	margin-left: 20px;
}
</style>
