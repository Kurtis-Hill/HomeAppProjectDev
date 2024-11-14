import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../../../Common/URLs/CommonURLs";

export interface CardUpdateRequestInterface {
    cardColour: number,
    cardIcon: number,
    cardViewState: number,
};

export async function updateCardRequest(cardViewID: number, cardUpdateRequest: CardUpdateRequestInterface): Promise<AxiosResponse> {
    const updateCardResponse: AxiosResponse = await axios.put(
        `${apiURL}card/${cardViewID}`,
        cardUpdateRequest
    );

    return updateCardResponse;
}
