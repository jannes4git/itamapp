import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";

import store from "./store/store.js";

/**
 * Service für Assets: CRUD-Operationen
 */
export const fetchAssets = async () => {
    try {
        const response = await axios.get(generateUrl("/apps/itamapp/assets"));
        store.commit("setInventar", response.data);
    } catch (e) {
        console.error(e);
    }
    try {
        const response = await axios.get(
            generateUrl("/apps/itamapp/customfields")
        );
        store.commit("setCustomFieldValues", response.data[1]);
        store.commit("setCustomFields", response.data[0]);
    } catch (e) {
        console.error(e);
    }
    try {
        const response = await axios.get(generateUrl("/apps/itamapp/raum"));
        store.commit("setRaum", response.data);
    } catch (e) {
        console.error(e);
    }
    try {
        const response = await axios.get(generateUrl("/apps/itamapp/person"));
        store.commit("setPersonen", response.data);
    } catch (e) {
        console.error(e);
    }
};
export async function postAsset(asset) {
    try {
        const {
            inventarnummer,
            rechnungsdatum,
            seriennummer,
            locationId,
            personId,
            customFieldValues,
        } = asset;

        const response = await axios.post(generateUrl("/apps/itamapp/assets"), {
            inventarnummer,
            rechnungsdatum,
            seriennummer,
            locationId,
            personId,
            customFieldValues,
        });

        return response.data;
    } catch (error) {
        throw error;
    }
}
export async function postAssets(assets) {
    try {
        const response = await axios.post(
            generateUrl("/apps/itamapp/csv"),
            assets
        );
        return response.data;
    } catch (error) {
        throw error;
    }
}
export async function editAsset(asset) {
    try {
        let {
            id,
            inventarnummer,
            rechnungsdatum,
            seriennummer,
            locationId,
            personId,
            customFieldValues,
        } = asset;
        if (asset.locationId === "null") {
            locationId = null;
        } else if (asset.personId === "null") {
            personId = null;
        }

        const response = await axios.put(
            generateUrl("/apps/itamapp/assets/" + id),
            {
                id,
                inventarnummer,
                rechnungsdatum,
                seriennummer,
                locationId,
                personId,
                customFieldValues,
            }
        );

        return response.data;
    } catch (error) {
        console.error(error);
    }
}

export async function deleteAsset(id) {
    try {
        const response = await axios.delete(
            generateUrl("/apps/itamapp/assets/" + id)
        );
        return response.data;
    } catch (error) {
        console.error(error);
    }
}
