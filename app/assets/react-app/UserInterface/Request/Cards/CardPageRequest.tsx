import axios, { AxiosResponse } from 'axios';

import { baseCardDataURL } from '../../../Common/URLs/CommonURLs';
import { CardFilterBarType } from '../../Components/Filterbars/CardFilterBarView';

export async function handleSendingCardDataRequest(props: { route:string; filterParams?: CardFilterBarType }): Promise<AxiosResponse> {
    const route:string = props.route ?? 'index';
    const filterParams = props.filterParams;
    
    let filterParamsObject: URLSearchParams|null = null;
    if (filterParams) {
        filterParamsObject = buildCardRequestFilters(filterParams);
    }

    return await axios.get(`${baseCardDataURL}${route}`, { params: filterParamsObject });
}

function buildCardRequestFilters(filterParams: CardFilterBarType): URLSearchParams {
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
