import * as React from 'react';
import { useState, useEffect } from 'react';

import axios, { AxiosResponse } from 'axios';

import { baseCardDataURL } from '../Common/CommonURLs';
import { CardFilterBarInterface } from '../Components/Filterbars/CardFilterBarInterface';

export async function handleSendingCardDataRequest(props: { route:string; filterParams?: CardFilterBarInterface|[] }): Promise<AxiosResponse> {
    const route:string = props.route ?? 'index';
    // const filterParams:string[] = props.filterParams ?? [];
    console.log('handleSending request filters', props.filterParams)
    
    return await axios.get(`${baseCardDataURL}${route}`);
}
