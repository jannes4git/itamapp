<template>
    <div class="container">
        <h2 style="margin-left: 50px">Person-Raum-Wechsel</h2>
        <div class="field">
            <h3 class="label"><u>Person</u></h3>
            <h3 class="input"><u>Raum</u></h3>
        </div>
        <div
            class="field"
            v-if="personenFetch"
            v-for="person in personen"
            :key="person.id"
        >
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
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import { fetchAssets } from "../AssetService";

export default {
    data() {
        return {
            personenFetch: null,
            selectedRaumIds: {},
        };
    },
    computed: {
        raeume() {
            return this.$store.getters.getRaum;
        },
        personen() {
            return this.$store.getters.getPersonen;
        },
    },
    methods: {
        async getPersonRaum() {
            const response = await axios.get(
                generateUrl("/apps/itamapp/person")
            );
            this.personenFetch = response.data;
            /**
             * Erstelle Mapping von PersonId zu RaumId.
             */
            this.personenFetch.forEach((person) => {
                this.selectedRaumIds[person.id] = person.locationId;
            });
        },
        /**
         * Führe den Raumwechsel durch.
         * @param personId
         */
        async updatePersonRaum(personId) {
            if (window.confirm("Wirklich ändern?")) {
                let raumId = this.selectedRaumIds[personId];
                if (raumId === "null") {
                    raumId = null;
                }
                const response = await axios.put(
                    generateUrl("/apps/itamapp/person/" + personId),
                    {
                        locationId: raumId,
                    }
                );
            }
        },
        getRaumIdForPerson(personId) {
            const person = this.personenFetch.find(
                (rp) => rp.personId === personId
            );
            return person.raumId;
        },
    },
    async created() {
        await fetchAssets();
        await this.getPersonRaum();
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
