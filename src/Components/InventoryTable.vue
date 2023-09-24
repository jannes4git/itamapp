<template>
	<div>
		<div class="table-container">
			<h2>Asset Tabelle</h2>
			<input type="text" v-model="search" placeholder="Suche..." />
			<button @click="exportCSV">Exportiere als CSV</button>
			<div class="table-scroll">
				<table class="table table--hoverable">
					<thead>
						<tr>
							<th>#</th>
							<th></th>
							<th><b>Inventarnummer</b></th>
							<th><b>Rechnungsdatum</b></th>
							<th><b>Seriennummer</b></th>
							<th><b>Raum</b></th>
							<th><b>Person</b></th>

							<th v-for="(field, id) in customFields" :key="id">
								<b>{{ field.name }}</b>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="(asset, index) in paginatedData"
							:key="asset.id"
							@click="push(asset.id, asset)">
							<td>{{ index + 1 }}</td>
							<td>
								<input type="checkbox" v-model="selected[index]" :value="asset.id" />
							</td>
							<td>{{ asset.inventarnummer }}</td>
							<td>{{ asset.rechnungsdatum }}</td>
							<td>{{ asset.seriennummer }}</td>
							<td>{{ getRaumName(asset.locationId) }}</td>
							<td>{{ getPersonName(asset.personId) }}</td>
							<td v-for="(field, id) in customFields" :key="id">
								{{ asset[field.name] }}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div>
			<button
				v-for="page in pages"
				:key="page"
				:disabled="currentPage === page"
				@click="changePage(page)">
				{{ page }}
			</button>
		</div>
	</div>
</template>

<script>
import { showError, showSuccess } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import { fetchAssets } from '../AssetService';
import store from '../store/store';
import { onActivated } from 'vue';
import { FilePicker, getFilePickerBuilder, FilePickerType } from '@nextcloud/dialogs';

export default {
	name: 'InventoryTable',
	props: {
		inventoryList: [],
	},
	data() {
		return {
			search: '',
			currentPage: 1,
			assetsPerPage: 30,
			selected: {},
			loading: true,
			//customFields: [],
			customFieldValues: [],
		};
	},
	methods: {
		getRaumName(locationId) {
			if (Array.isArray(this.raeume)) {
				const raum = this.raeume.find((r) => r.id === Number(locationId));
				return raum ? raum.raumName : '';
			}
			return '';
		},
		getPersonName(personId) {
			if (Array.isArray(this.personen)) {
				const person = this.personen.find((p) => p.id === Number(personId));
				return person ? person.name : '';
			}
			return '';
		},
		exportCSV() {
			let csv = this.$store.state.assets.defaultAssetFields.join() + ',';
			csv += this.customFields.map((field) => field.name).join(',') + '\n';
			this.inventar.forEach((asset) => {
				csv += `${asset.inventarnummer},${asset.rechnungsdatum},${
					asset.seriennummer
				},${this.getRaumName(asset.locationId)},${this.getPersonName(asset.personId)},`;
				csv += this.customFields
					.map((field) => asset[field.name])
					.join(',');
				csv += '\n';
			});

			let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
			let link = document.createElement('a');
			let url = URL.createObjectURL(blob);
			link.setAttribute('href', url);
			link.setAttribute('download', 'export.csv');
			link.style.visibility = 'hidden';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		},
		createCSV() {},
		changePage(page) {
			this.currentPage = page;
		},
		logData(data) {
			console.log(data);
		},
		push(id, asset) {
			this.$router.push({
				name: 'AssetDetail',
				params: { id: id },
				props: { asset: asset },
			});
		},
		async test() {
			const builder = getFilePickerBuilder('Wählen Sie eine PDF-Datei aus')
				.setMultiSelect(false)
				.setModal(true)
				.setType(FilePickerType.Choose)
				.allowDirectories(false);

			const picker = builder.build();
			this.selectedPdfPath = await picker.pick();
			console.log('Selected PDF: ' + this.selectedPdfPath);

			//const response = await axios.get(generateUrl('/apps/itamapp/personraum'));
			//console.log('PersonRaum: ', response.data);
		},
		editSelected() {
			console.log('Selected items:', this.selected);
		},
	},
	computed: {
		pages() {
			return Math.ceil(this.filteredInventar.length / this.assetsPerPage);
		},
		paginatedData() {
			const start = (this.currentPage - 1) * this.assetsPerPage;
			const end = start + this.assetsPerPage;
			return this.filteredInventar.slice(start, end);
		},
		filteredInventar() {
			if (this.search === '') {
				return this.inventar;
			} else {
				return this.inventar.filter((asset) => {
					let raum = this.getRaumName(asset.locationId);
					let person = this.getPersonName(asset.personId);
					//Prüfen der default Felder
					const matchesInDefaultFields =
						(asset.inventarnummer && asset.inventarnummer.includes(this.search)) ||
						(asset.rechnungsdatum && asset.rechnungsdatum.includes(this.search)) ||
						(asset.seriennummer && asset.seriennummer.includes(this.search)) ||
						(asset.beschreibung && asset.beschreibung.includes(this.search)) ||
						(asset.locationId && raum.includes(this.search)) ||
						(asset.personId && person.includes(this.search));

					if (matchesInDefaultFields) {
						return true;
					}

					//Prüfen der CustomFields
					const matchesInCustomFields = this.customFields.some((customField) => {
						const cfName = customField.name;
						//überprüfe auch asset[cfName] auf null, da viele Assets nicht alle CustomFields haben
						return asset[cfName] && asset[cfName].includes(this.search);
					});

					return matchesInCustomFields;
				});
			}
		},
		inventar() {
			var groupedCustomFields = {};
			var assets = this.$store.state.assets.inventar;
			var customFields = this.$store.state.assets.customFieldValues;
			//Ordne die CustomFieldValues den zugehörigen Assets zu
			customFields.forEach((field) => {
				if (!groupedCustomFields[field.assetId]) {
					groupedCustomFields[field.assetId] = [];
				}
				groupedCustomFields[field.assetId].push(field);
			});
			//Füge die CustomFieldValues den Asset-Objekten hinzu
			assets.forEach((asset) => {
				var cfAsset = groupedCustomFields[asset.id];
				if (cfAsset === undefined) {
					return;
				}
				cfAsset.forEach((cf) => {
					asset[cf.name] = cf.value;
				});
				asset.customFields = groupedCustomFields[asset.id];
			});
			return assets;
		},
		raeume() {
			return this.$store.getters.getRaum;
		},
		personen() {
			return this.$store.getters.getPersonen;
		},
		customFields() {
			return this.$store.state.assets.customFields;
		},
	},
	async created() {
		await fetchAssets();
		console.log('Created ' + this.inventoryList);
	},
	async mounted() {
		/*
    //TODO: get Columns und Daten
    console.log("MOunting");
    try {
      const response = await axios.get(
        generateUrl("/apps/itamapp/assets")
      );
      //console.log("Hallo Jannes: " + response.data.inventarnummer);
      this.inventar = response.data;
      console.log("Inventar "+this.inventar);

    } catch (e) {
      console.error(e);
      showError(t("notestutorial", "Could not fetch assets"));
    }
    try {
      const response = await axios.get(
        generateUrl("/apps/itamapp/customfields")
      );
      //TODO: andere Möglichkeit vlt bessere Laufzeit. API call auf assets und customfields zusammen dann group by asset und halt pro zeile ein asset mit einem customfield
      //VLT DB Prof eine Email schicken und fragen wie er es lösen würde
      this.customFields = response.data[1];
      this.fields = response.data[0];
      //console.log("Fields: " +this.fields);
      this.fields.forEach((field) => {
        console.log("Field: " + field.name);
      });

      var groupedCustomFields = {};
      this.customFields.forEach((field) => {
        //console.log(" Hiier"+field.asset_id);
        if (!groupedCustomFields[field.asset_id]) {
          groupedCustomFields[field.asset_id] = [];
        }
        groupedCustomFields[field.asset_id].push(field);
      });
      console.log(groupedCustomFields);
      this.inventar.forEach((asset) => {
        var cfAsset = groupedCustomFields[asset.id];
        cfAsset.forEach((cf) => {
          asset[cf.name] = cf.value;
        });
        //console.log("Hallooooooo"+name);
        asset.customFields = groupedCustomFields[asset.id];
      });
    } catch (e) {
      console.error(e);
      showError(t("notestutorial", "Could not fetch customfields"));
    }
    */
		/*
    console.log("Mounted Table");
    this.inventar = store.getters.getAssets;
    this.fields = store.getters.getCustomFields;
    this.customFields = store.getters.getCustomFieldValues;
    this.fields.forEach((field) => {
      console.log("Field: " + field.name);
    });

    var groupedCustomFields = {};
    this.customFields.forEach((field) => {
      //console.log(" Hiier"+field.asset_id);
      if (!groupedCustomFields[field.asset_id]) {
        groupedCustomFields[field.asset_id] = [];
      }
      groupedCustomFields[field.asset_id].push(field);
    });
    console.log(groupedCustomFields);
    this.inventar.forEach((asset) => {
      var cfAsset = groupedCustomFields[asset.id];
      cfAsset.forEach((cf) => {
        asset[cf.name] = cf.value;
      });
      //console.log("Hallooooooo"+name);
      asset.customFields = groupedCustomFields[asset.id];
    });
    this.loading = false;
    */
	},
};
</script>

<style>
.table-container {
	width: 100%;
	margin: 0 auto;
	margin-top: 40px;
	padding-left: 1%;
	padding-right: 1%;
	position: relative;
}

.table {
	width: 100%;
	border-collapse: collapse;
	margin-top: 10px;
}

table th,
table td {
	padding: 6px;
	text-align: left;
	border: 1px solid #c5c3c3;
}

.table--hoverable tbody tr:hover {
	background-color: #f4f8fd;
}
.table-scroll {
	overflow: auto;
	max-height: calc(100vh - 250px);
}
</style>
