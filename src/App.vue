<template>
    <NcContent app-name="itamapp">
        <NcAppNavigation>
            <router-link :to="'/'">
                <NcAppNavigationNew
                    class="invent"
                    text="Asset Tabelle"
                ></NcAppNavigationNew>
            </router-link>
            <router-link :to="'/personRaum'">
                <NcAppNavigationNew
                    :editable="true"
                    class="personRaum"
                    text="Person-Raum-Wechsel"
                ></NcAppNavigationNew>
            </router-link>
            <router-link :to="'/importCSV'">
                <NcAppNavigationNew
                    class="importCSV"
                    text="CSV-Import"
                ></NcAppNavigationNew>
            </router-link>

            <template #footer>
                <router-link :to="'/newAsset'">
                    <NcAppNavigationNew
                        :text="t('itamapp', 'Assets')"
                        :disabled="false"
                        button-id="asset-itamapp-button"
                        button-class="icon-add"
                    >
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
                        button-class="icon-add"
                    >
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
                        button-class="icon-add"
                    >
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
                        button-class="icon-add"
                    >
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
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton";
import NcContent from "@nextcloud/vue/dist/Components/NcContent";
import NcAppContent from "@nextcloud/vue/dist/Components/NcAppContent";
import NcAppNavigation from "@nextcloud/vue/dist/Components/NcAppNavigation";
import NcAppNavigationItem from "@nextcloud/vue/dist/Components/NcAppNavigationItem";
import NcAppNavigationNew from "@nextcloud/vue/dist/Components/NcAppNavigationNew";
import PlusIcon from "vue-material-design-icons/Plus.vue";
import Pencil from "vue-material-design-icons/Pencil.vue";

import "@nextcloud/dialogs/styles/toast.scss";

import AssetTable from "./Components/AssetTable.vue";

import { fetchAssets } from "./AssetService.js";

export default {
    name: "App",
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
        await fetchAssets();
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

input[type="text"] {
    width: 100%;
}

textarea {
    flex-grow: 1;
    width: 100%;
}

.router-link-exact-active {
    background-color: #b0d8ed;
}
</style>
