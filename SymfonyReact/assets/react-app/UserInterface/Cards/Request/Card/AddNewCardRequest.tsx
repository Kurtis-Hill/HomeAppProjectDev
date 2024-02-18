import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../../../Common/URLs/CommonURLs";

export type AddNewCardType = {
    sensorID: number|null,
    cardColour?: number|null,
    cardIcon?: number|null,
    cardViewState?: number|null,
}

export async function addNewCardRequest(addNewCardData: AddNewCardType): Promise<AxiosResponse> {
    const addNewCardResponse: AxiosResponse = await axios.post(
        `${apiURL}card/add`,
        addNewCardData,
    );

    return addNewCardResponse;
}