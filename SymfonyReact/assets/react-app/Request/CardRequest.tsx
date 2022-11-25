import * as React from 'react';
import { useState, useEffect } from 'react';

import axios, { AxiosResponse } from 'axios';

import { baseCardDataURL } from '../Common/CommonURLs';

export async function handleSendingCardDataRequest(props: { route:string; filterParams?: string[] }): Promise<AxiosResponse> {
    const route:string = props.route ?? 'index';
    const filterParams:string[] = props.filterParams ?? [];
    
    return await axios.get(`${baseCardDataURL}${route}`);
}
