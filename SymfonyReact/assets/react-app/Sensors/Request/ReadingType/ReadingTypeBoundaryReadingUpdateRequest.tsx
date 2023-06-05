import axios, { AxiosResponse } from 'axios';
import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import { apiURL } from '../../../Common/URLs/CommonURLs';

export async function readingTypeBoundaryReadingUpdateRequest(props: {sensorID: number}): Promise<AxiosResponse> {
    const { sensorID } = props;
    
    const sensorReadingUpdateRequestResponse: AxiosResponse = await axios.put(
        `${apiURL}sensor/${sensorID}/boundary-update`,
    );

    return sensorReadingUpdateRequestResponse;
}