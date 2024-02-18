import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../../../Common/URLs/CommonURLs";

export type CardUpdateRequestType = {
    cardColour: number,
    cardIcon: number,
    cardViewState: number,
};

export async function updateCardRequest(cardViewID: number, cardUpdateRequest: CardUpdateRequestType): Promise<AxiosResponse> {
    const updateCardResponse: AxiosResponse = await axios.put(
        `${apiURL}card/${cardViewID}/update`,
        cardUpdateRequest
    );

    return updateCardResponse;
}