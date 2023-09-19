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
			<div v-for="(feld, index) in dbArray" :key="index" class="fields">
				<p>{{ feld }}</p>
				<select v-model="selected[index]" @change="handleChange(index)">
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
		</div>
	</div>
</template>

<script>
import Papa from 'papaparse';
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import fuzzysort from 'fuzzysort';
import { postAsset, postAssets } from '../../AssetService';

export default {
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
			dbArray: [],
			defaultFelder: ['Inventarnummer', 'Rechnungsdatum', 'Seriennummer'],
			customFelder: [],
			csvFields: [],
			selected: [],
			dbInfo: {},
			daten: null,
		};
	},
	created() {},
	methods: {
		select() {
			console.log(t);
			this.selected = Array(this.dbArray.length).fill('');
		},
		async importCSV() {
			console.log('Import CSV');
			//console.log(this.csvData);
			//console.log(this.csvFields);
			//console.log("Datenbank Array " + this.dbArray);
			//console.log("Datenbank Default " + this.defaultFelder);
			//console.log("Datenbank Custom " + this.customFelder);
			//console.log("Selektiert: " + this.selected);
			//Inventarnummer:
			var inventarnummer = this.selected[this.dbArray.indexOf('Inventarnummer')];
			//console.log('Inventarnummer: ' + inventarnummer);
			var rechnungsdatum = this.selected[this.dbArray.indexOf('Rechnungsdatum')];
			//console.log('Rechnungsdatum: ' + rechnungsdatum);
			var seriennummer = this.selected[this.dbArray.indexOf('Seriennummer')];
			console.log('Seriennummer: ' + seriennummer);

			//console.log('CSV Data: ' + JSON.stringify(this.csvData));
			let count = 0;
			let allAssets = [];
			for (let row of this.csvData) {
				//console.log('Count: ' + count);
				if (count == 5) {
					//break;
				}
				count++;
				console.log('Row: ' + JSON.stringify(row));
				var asset = {};
				asset = {
					inventarnummer: row[inventarnummer],
					rechnungsdatum: row[rechnungsdatum],
					seriennummer: row[seriennummer],
					customFieldValues: {},
				};
				for (let i = 3; i < this.dbArray.length; i++) {
					console.log('i: ' + i);
					//if (this.selected[i] != "") {
					//console.log('Selected: ' + row[this.selected[i]]);
					asset.customFieldValues[this.dbArray[i]] = row[this.selected[i]];
					//}
				}
				//console.log('Custom Field Values: ' + asset.customFieldValues);
				/*
        Object.keys(asset).forEach((key) => {
          console.log("Key:", key);
          console.log("Value:", asset[key]);
        });
        */
				console.log(asset);
				if (this.hasValidValue(asset)) {
					allAssets.push(asset);
				}

				//await postAsset(asset);
			}
			this.postCSV(allAssets);
			console.log('All Assets: ' + JSON.stringify(allAssets));
			console.log('Import fertig');
		},
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
		isDisabled(csvColumn) {
			return this.selected.includes(csvColumn);
		},
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
			this.dbArray.forEach((dbColumn, index) => {
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

			/*
      this.daten.forEach((dbColumn, index) => {
        const dbColumnSanitized = dbColumn.field
          .toLowerCase()
          .replace(/\s+/g, "");
        const matchingCsvColumn = this.csvFields.find(
          (csvColumn) =>
            csvColumn.toLowerCase().replace(/\s+/g, "") === dbColumnSanitized
        );
        if (matchingCsvColumn) {
          this.selected[index] = matchingCsvColumn;
        } else {
          const bestMatch = fuzzysort.go(
            dbColumnSanitized,
            this.csvFields.map((csvColumn) =>
              csvColumn.toLowerCase().replace(/\s+/g, "")
            ),
            { limit: 1 }
          )[0];
          if (bestMatch && bestMatch.score > -10000) {
            // Sie können den Schwellenwert anpassen, um die Sensibilität des Fuzzy-Matching zu steuern
            this.selected[index] = this.csvColumns[bestMatch.index];
          }


        }

      });
      */
		},
		getMapping() {
			let mapping = {};
			for (let i = 0; i < this.dbArray.length; i++) {
				mapping[this.dbArray[i]] = this.selected[i];
			}
			for (let type in this.dbInfo) {
				let table = type === 'default' ? 'default_table' : 'custom_table';
				console.log(table);
			}
			return mapping;
		},
		mapFields() {
			// Implement custom mapping logic here

			console.log('Selected: ' + this.selected);
			console.log('DB Array: ' + this.dbArray);
			console.log(this.getMapping());
		},
		availableCsvColumns(index) {
			const selectedColumns = this.selected.filter(
				(selected, selectedIndex) => selectedIndex !== index && selected !== 'none'
			);
			return this.csvFields.filter((csvColumn) => !selectedColumns.includes(csvColumn));
		},
		handleChange(index) {
			console.log('handleChange', index);
			this.$set(this.selected, index, this.selected[index]);
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
				this.dbArray.push(element);
			});
			this.customFelder.forEach((element) => {
				this.dbArray.push(element.name);
				this.daten[element.name] = {
					field: element.name,
					type: 'custom',
					table: 'custom_table',
				};
			});
			this.select(); //console.log(this.dbColumns[1][0].name);
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
