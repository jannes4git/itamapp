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
            <button type="submit" :disabled="form.name === ''">
                Speichern
            </button>
        </form>
        <table>
            <thead>
                <tr>
                    <th><b>Name</b></th>
                    <th><b>Aktion</b></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cf in customFields" :key="cf.id">
                    <td>{{ cf.name }}</td>
                    <td>
                        <button @click="deleteCF(cf.id, cf.name)">
                            Löschen
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
export default {
    data() {
        return {
            customFields: [],
            form: {
                name: "",
                type: "string",
            },
        };
    },
    methods: {
        async submitForm() {
            if (window.confirm(this.form.name + " wirklich erstellen?")) {
                const response = await axios.post(
                    generateUrl("/apps/itamapp/customfields"),
                    {
                        name: this.form.name,
                        type: this.form.type,
                    }
                );
                this.getCF();
            }
        },
        async getCF() {
            const response = await axios.get(
                generateUrl("/apps/itamapp/customfields")
            );
            this.customFields = response.data[0];
        },
        async deleteCF(id, name) {
            if (
                window.confirm(
                    "Wollen Sie das Custom Field " +
                        name +
                        " wirklich löschen?\Es werden auch alle zugehörigen Daten gelöscht!"
                )
            ) {
                axios.delete(generateUrl("/apps/itamapp/customfields/" + id));
                await this.getCF();
            }
        },
    },
    async mounted() {
        await this.getCF();
    },
};
</script>

<style scoped>
.container {
    margin-left: 2%;
    margin-top: 2%;
}
</style>
