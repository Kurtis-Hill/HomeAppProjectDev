import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../../../Common/URLs/CommonURLs";

export interface AddNewCardTypeInterface {
    sensorID: number|null,
    cardColour?: number|null,
    cardIcon?: number|null,
    cardViewState?: number|null,
}

export async function addNewCardRequest(addNewCardData: AddNewCardTypeInterface): Promise<AxiosResponse> {
    const addNewCardResponse: AxiosResponse = await axios.post(
        `${apiURL}card`,
        addNewCardData,
    );

    return addNewCardResponse;
}
