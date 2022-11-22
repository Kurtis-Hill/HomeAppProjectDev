import * as React from 'react';
import { useState, useEffect } from 'react';

import axios, { AxiosResponse } from 'axios';

import { baseCardDataURL } from '../Common/CommonURLs';

export async function handleSendingCardDataRequest(props: { route:string }): Promise<AxiosResponse> {
    const route:string = props.route ?? 'index';
    
    return await axios.get(`${baseCardDataURL}${route}`);
}
