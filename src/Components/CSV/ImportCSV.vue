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
                <select v-model="mapping[index]" @change="handleChange()">
                    <option disabled value="">Nicht zugeordnet</option>
                    <option value="none">Nicht zuordnen</option>
                    <option
                        v-for="(csvColumn, i) in availableCsvColumns(index)"
                        :key="i"
                        :value="csvColumn"
                        :disabled="isDisabled(csvColumn)"
                    >
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
                        <p>
                            Entweder Rechnungsdatum oder Inventarnummer muss
                            zugeordnet werden. <br />Andernfalls kann die
                            Inventarnummer nicht eingefügt oder erstellt werden.
                            <br />
                            Falls nur das Rechnungsdatum zugeordnet wird und
                            dieses in der CSV nicht in jeder Zeile vorhanden
                            ist, wird der Import abgebrochen.
                        </p>
                    </template>
                </NcPopover>
            </div>
        </div>
    </div>
</template>

<script>
import Papa from "papaparse";
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import fuzzysort from "fuzzysort";
import { postAssets } from "../../AssetService";
import NcPopover from "@nextcloud/vue/dist/Components/NcPopover.js";
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";

export default {
    components: {
        NcPopover,
        NcButton,
    },
    async mounted() {
        await this.getColumns();
    },
    data() {
        return {
            csvData: null,
            dbFields: [],
            defaultFelder: ["Inventarnummer", "Rechnungsdatum", "Seriennummer"],
            customFelder: [],
            csvFields: [],
            /**
             * Array mit den zugeordneten CSV Feldern: dbArray ist der index und der Wert ist das zugeordnete CSV Feld
             */
            mapping: [],
            dbInfo: {},
        };
    },
    created() {},
    methods: {
        initMapping() {
            this.mapping = Array(this.dbFields.length).fill("");
        },
        /**
         * Importiert die zugeordneten CSV Felder in die Datenbank
         */
        async importCSV() {
            console.log("Import CSV");
            if (!window.confirm("CSV wird importiert")) {
                return;
            }
            //Default-Feld-Zuordnungen:
            var inventarnummer =
                this.mapping[this.dbFields.indexOf("Inventarnummer")];
            var rechnungsdatum =
                this.mapping[this.dbFields.indexOf("Rechnungsdatum")];
            var seriennummer =
                this.mapping[this.dbFields.indexOf("Seriennummer")];
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
                    asset.customFieldValues[this.dbFields[i]] =
                        row[this.mapping[i]];
                }
                //Füge das Asset dem Asset-Array hinzu falls es valide Werte enthält
                if (this.hasValidValue(asset)) {
                    allAssets.push(asset);
                }
            }
            /**
             * Importiere Assets der CSV und verarbeite die Antwort
             */
            try {
                let response = await postAssets(allAssets);
                alert(
                    "Import von " +
                        JSON.stringify(response) +
                        " Assets erfolgreich"
                );
                this.$router.push("/");
            } catch (error) {
                if (error.response.data.existingInventarnummern) {
                    alert(
                        "Import fehlgeschlagen: " +
                            error.response.data.message +
                            "\nDuplikate:\n" +
                            error.response.data.existingInventarnummern
                    );
                } else if (error.response.data.zeilen) {
                    alert(
                        "Import fehlgeschlagen: " +
                            error.response.data.message +
                            "\nBitte überprüfen Sie die CSV Datei in den Zeilen:\n" +
                            error.response.data.zeilen
                    );
                } else {
                    alert(
                        "Import fehlgeschlagen: " + error.response.data.message
                    );
                }
            }
            console.log("Import fertig");
        },
        /**
         * Check ob ein Asset gültige Werte enthält.
         * @param asset
         */
        hasValidValue(asset) {
            for (let key in asset) {
                if (asset.hasOwnProperty(key)) {
                    let value = asset[key];
                    // Überprüfe ob der Wert ein String und nicht leer ist
                    if (typeof value === "string" && value.trim() !== "") {
                        return true;
                    }

                    // Wenn der Wert ein Objekt ist (CustomFieldValue-Objekt), überprüfe ob dieses Objekt einen nicht leeren Wert enthält
                    if (typeof value === "object") {
                        for (let subKey in value) {
                            if (
                                value.hasOwnProperty(subKey) &&
                                value[subKey] &&
                                value[subKey].trim() !== ""
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
        },
        /**
         * Check ob ein CSV Feld bereits zugeordnet wurde.
         * @param csvColumn
         */
        isDisabled(csvColumn) {
            return this.mapping.includes(csvColumn);
        },
        /**
         * Liest die CSV Datei ein, speichert die Felder in csvFields und die Daten als Objekte in csvData.
         * Führt dann eine automatische Zuordnung durch.
         *
         * @param event
         */
        handleFileUpload(event) {
            const file = event.target.files[0];

            Papa.parse(file, {
                complete: (results) => {
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
        /**
         * Erste automatische Zuordnung der CSV Felder zu den Datenbankfeldern
         */
        autoMapColumns() {
            this.dbFields.forEach((dbField, index) => {
                //Überprüfe auf direkte Übereinstimmung
                const dbFieldForCheck = dbField
                    .toLowerCase()
                    .replace(/\s+/g, "");
                const matchingCsvColumn = this.csvFields.find(
                    (csvField) =>
                        csvField.toLowerCase().replace(/\s+/g, "") ===
                        dbFieldForCheck
                );
                if (matchingCsvColumn) {
                    this.mapping[index] = matchingCsvColumn;
                } else {
                    //Überprüfe auf ähnliche Übereinstimmung
                    const bestMatch = fuzzysort.go(
                        dbFieldForCheck,
                        this.csvFields.map((csvField) =>
                            csvField.toLowerCase().replace(/\s+/g, "")
                        ),
                        { limit: 1 }
                    )[0];
                    //Überprüfe ob Übereinstimmung besteht und ob der Schwellenwert erreicht wurde
                    if (bestMatch && bestMatch.score > -4) {
                        const matchingIndex = this.csvFields.findIndex(
                            (csvColumn) =>
                                csvColumn.toLowerCase().replace(/\s+/g, "") ===
                                bestMatch.target
                        );
                        if (matchingIndex !== -1) {
                            this.mapping[index] = this.csvFields[matchingIndex];
                        }
                    }
                }
            });
        },
        /**
         * Filter noch schon zugeordnete Werte aus der Liste der CSV Felder.
         * @param index
         */
        availableCsvColumns(index) {
            const selectedColumns = this.mapping.filter(
                (map, mapIndex) => mapIndex !== index && map !== "none"
            );
            return this.csvFields.filter(
                (csvColumn) => !selectedColumns.includes(csvColumn)
            );
        },
        handleChange() {},
        /**
         * Holt die Datenbankfelder aus dem Array 'defaultFelder' und der Datenbank (Custom Fields).
         * Initialisiert anschließend das Mapping-Array.
         */
        async getColumns() {
            var columnsDB = (await axios.get(generateUrl("/apps/itamapp/meta")))
                .data;
            this.customFelder = columnsDB;

            this.defaultFelder.forEach((element) => {
                this.dbFields.push(element);
            });
            this.customFelder.forEach((element) => {
                this.dbFields.push(element.name);
            });
            this.initMapping();
        },
    },
};
</script>

<style scoped>
.fields,
.headers {
    display: flex;
    align-items: center;
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
