<template>
	<!--
    SPDX-FileCopyrightText: Jannes Lensch <test@test.de>
    SPDX-License-Identifier: AGPL-3.0-or-later
    -->
	<NcContent app-name="itamapp">
		<NcAppNavigation>
			<router-link :to="'/'">
				<NcAppNavigationNew class="invent" text="Asset Tabelle"></NcAppNavigationNew>
			</router-link>
			<router-link :to="'/personRaum'">
				<NcAppNavigationNew
					:editable="true"
					class="personRaum"
					text="Person-Raum-Wechsel"></NcAppNavigationNew>
			</router-link>
			<router-link :to="'/importCSV'">
				<NcAppNavigationNew class="importCSV" text="CSV-Import"></NcAppNavigationNew>
			</router-link>

			<template #footer>
				<router-link :to="'/newAsset'">
					<NcAppNavigationNew
						:text="t('itamapp', 'Assets')"
						:disabled="false"
						button-id="asset-itamapp-button"
						button-class="icon-add">
						<template #icon>
							<PlusIcon />
						</template>
					</NcAppNavigationNew>
				</router-link>

				<router-link :to="'/newPerson'">
					<NcAppNavigationNew
						:text="t('itamapp', 'Person')"
						:disabled="false"
						button-id="asset-itamapp-button"
						button-class="icon-add">
						<template #icon>
							<Pencil />
						</template>
					</NcAppNavigationNew>
				</router-link>
				<router-link :to="'/newRaum'">
					<NcAppNavigationNew
						:text="t('itamapp', 'Raum')"
						:disabled="false"
						button-id="asset-itamapp-button"
						button-class="icon-add">
						<template #icon>
							<Pencil />
						</template>
					</NcAppNavigationNew>
				</router-link>
				<router-link :to="'/newCustomField'">
					<NcAppNavigationNew
						text="Custom Feld"
						:disabled="false"
						button-id="cf-itamapp-button"
						button-class="icon-add">
						<template #icon>
							<Pencil />
						</template>
					</NcAppNavigationNew>
				</router-link>
			</template>

		</NcAppNavigation>

		<NcAppContent>
			<router-view />
		</NcAppContent>
	</NcContent>
</template>

<script>
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton';
import NcContent from '@nextcloud/vue/dist/Components/NcContent';
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent';
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation';
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem';
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew';
import PlusIcon from 'vue-material-design-icons/Plus.vue';
import Pencil from 'vue-material-design-icons/Pencil.vue';

import '@nextcloud/dialogs/styles/toast.scss';
import { generateUrl } from '@nextcloud/router';
import { showError, showSuccess } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';
import AssetTable from './Components/AssetTable.vue';

import { fetchAssets } from './AssetService.js';
import store from './store/store';

export default {
	name: 'App',
	components: {
		NcActionButton,
		NcAppContent,
		NcContent,
		NcAppNavigation,
		NcAppNavigationItem,
		NcAppNavigationNew,
		AssetTable,
		PlusIcon,
		Pencil,
		Pencil,
	},
	async beforeMount() {
		//console.log(store.getters.getAssets);
		console.log('created App.vue');

		await fetchAssets();
	},
	data() {
		return {
			currentNoteId: null,
			updating: false,
		};
	},
	computed: {
		assets() {
			//console.log("Assets: "+store.getters.assets);
			return store.getters.assets;
		},
	},

	methods: {
		newAsset() {
			alert('new Asset');
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * @param {Object} note Note object
		 */
		openNote(note) {
			if (this.updating) {
				return;
			}
			this.currentNoteId = note.id;
			this.$nextTick(() => {
				this.$refs.content.focus();
			});
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		saveNote() {
			if (this.currentNoteId === -1) {
				this.createNote(this.currentNote);
			} else {
				this.updateNote(this.currentNote);
			}
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * The note is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNote() {
			if (this.currentNoteId !== -1) {
				this.currentNoteId = -1;
				this.notes.push({
					id: -1,
					title: '',
					content: '',
				});
				this.$nextTick(() => {
					this.$refs.title.focus();
				});
			}
		},
		/**
		 * Abort creating a new note
		 */
		cancelNewNote() {
			this.notes.splice(
				this.notes.findIndex((note) => note.id === -1),
				1
			);
			this.currentNoteId = null;
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createNote(note) {
			this.updating = true;
			try {
				const response = await axios.post(
					generateUrl('/apps/itamapp/notes'),
					note
				);
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId);
				this.$set(this.notes, index, response.data);
				this.currentNoteId = response.data.id;
			} catch (e) {
				console.error(e);
				showError(t('notestutorial', 'Could not create the note'));
			}
			this.updating = false;
		},
		/**
		 * Update an existing note on the server
		 * @param {Object} note Note object
		 */
		async updateNote(note) {
			this.updating = true;
			try {
				await axios.put(generateUrl(`/apps/itamapp/notes/${note.id}`), note);
			} catch (e) {
				console.error(e);
				showError(t('notestutorial', 'Could not update the note'));
			}
			this.updating = false;
		},
		/**
		 * Delete a note, remove it from the frontend and show a hint
		 * @param {Object} note Note object
		 */
		async deleteNote(note) {
			try {
				await axios.delete(generateUrl(`/apps/itamapp/notes/${note.id}`));
				this.notes.splice(this.notes.indexOf(note), 1);
				if (this.currentNoteId === note.id) {
					this.currentNoteId = null;
				}
				showSuccess(t('itamapp', 'Note deleted'));
			} catch (e) {
				console.error(e);
				showError(t('itamapp', 'Could not delete the note'));
			}
		},
	},
};
</script>
<style scoped>
#app-content > div {
	width: 100%;
	height: 100%;
	padding: 20px;
	display: flex;
	flex-direction: column;
	flex-grow: 1;
}

input[type='text'] {
	width: 100%;
}

textarea {
	flex-grow: 1;
	width: 100%;
}
</style>
