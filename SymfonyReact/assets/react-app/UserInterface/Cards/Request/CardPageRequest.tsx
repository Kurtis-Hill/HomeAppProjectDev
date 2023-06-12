import * as React from 'react';
import { useState, useEffect } from 'react';

import axios, { AxiosResponse } from 'axios';

import { baseCardDataURL } from '../../../Common/URLs/CommonURLs';
import { CardFilterBarInterface } from '../Components/Filterbars/CardFilterBarInterface';

export async function handleSendingCardDataRequest(props: { route:string; filterParams?: CardFilterBarInterface }): Promise<AxiosResponse> {
    const route:string = props.route ?? 'index';
    const filterParams = props.filterParams;
    
    let filterParamsObject: URLSearchParams|null = null;
    if (filterParams) {
        filterParamsObject = buildCardRequestFilters(filterParams);
    }

    return await axios.get(`${baseCardDataURL}${route}`, { params: filterParamsObject });
}

function buildCardRequestFilters(filterParams: CardFilterBarInterface): URLSearchParams {
    const typeGetParamsObject = new URLSearchParams();

    if (filterParams.sensorTypes && filterParams.sensorTypes.length > 0) {
        for (let i = 0; i < filterParams.sensorTypes.length; i++) {
            typeGetParamsObject.append('sensor-types[]', filterParams.sensorTypes[i]);
        }
    }

    if (filterParams.readingTypes && filterParams.readingTypes.length > 0) {
        for (let i = 0; i < filterParams.readingTypes.length; i++) {
            typeGetParamsObject.append('reading-types[]', filterParams.readingTypes[i]);
        }
    }

    return typeGetParamsObject;
}
