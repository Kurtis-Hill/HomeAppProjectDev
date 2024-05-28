import axios, { AxiosResponse } from 'axios';

import { apiURL } from "../../../../Common/URLs/CommonURLs";

export async function getCardViewFormRequest(cardViewID: number): Promise<AxiosResponse> {
    const getCardViewFormResponse: AxiosResponse = await axios.get(
        `${apiURL}card-form/${cardViewID}/get`,
    );

    return getCardViewFormResponse;
}