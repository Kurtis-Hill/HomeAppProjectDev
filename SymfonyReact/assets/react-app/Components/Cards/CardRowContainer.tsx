import * as React from 'react';
import { useState } from 'react';

import { CardReadingHandler } from './Readings/CardReadingHandler';
import { CardFilterBarInterface } from '../Filterbars/CardFilterBarInterface';
import CardFilterBar from '../Filterbars/CardFilterBar';

export function CardRowContainer(props: { 
    route?: string;
    filterParams?: CardFilterBarInterface;
    horizontal?: boolean; 
    classes?: string; 
}) {
    const [sensorFilterParams, setSensorFilterParams] = useState<CardFilterBarInterface>(props.filterParams ?? {readingTypes: [], sensorTypes: []});

    const [cardRefreshTimer, setCardRefreshTimer] = useState<number>(4000)

    const route: string = props.route ?? 'index';

    const horizontal: boolean = props.horizontal ?? false;

    const classes: string = props.classes;

    const addSensorFilterParamsForRequest = (filterParam: {type: string, value: string}): void => {
        const filterParamType = filterParam.type;

        if (filterParamType === 'readingType') {
            setSensorFilterParams({...sensorFilterParams, readingTypes: [...sensorFilterParams.readingTypes, filterParam.value]});
        }
        if (filterParamType === 'sensorType') {
            setSensorFilterParams({...sensorFilterParams, sensorTypes: [...sensorFilterParams.sensorTypes, filterParam.value]});
        }
    };

    const removeSensorFilterParamsForRequest = (filterParam: {type: string, value: string}): void => {
        if (filterParam.type === 'readingType') {
            console.log('here at least', filterParam.value)
            if (Array.isArray(sensorFilterParams.readingTypes) && sensorFilterParams.readingTypes.includes(filterParam.value, sensorFilterParams)) {
                sensorFilterParams.readingTypes.splice(sensorFilterParams.readingTypes.indexOf(filterParam.value), 1);
                setSensorFilterParams({...sensorFilterParams, readingTypes: sensorFilterParams});
            }
        }
        if (filterParam.type === 'sensorType') {
            if (Array.isArray(sensorFilterParams.sensorTypes) && sensorFilterParams.sensorTypes.includes(filterParam.value, sensorFilterParams)) {
                sensorFilterParams.sensorTypes.splice(sensorFilterParams.sensorTypes.indexOf(filterParam.value), 1);
                setSensorFilterParams({...sensorFilterParams, sensorTypes: sensorFilterParams});
            }
        }
    };

    const buildCardContainer = (): React => {
        if (horizontal === true) {
            return (
                <div className={classes ?? 'col-xl-12 col-md-12 mb-12'}>
                    { buildCardReadingHandler() }
                </div>                
            );
        } else {
            return (
                <>
                    { buildCardReadingHandler() }
                </>
            );
        }
    };

    const buildCardReadingHandler = (): React => {
        return (
            <CardReadingHandler 
                route={route} 
                filterParams={sensorFilterParams} 
                cardRefreshTimer={cardRefreshTimer} 
            />
        );
    }

    return (
        <>
            <CardFilterBar 
                filterParams={sensorFilterParams} 
                addFilterParams={addSensorFilterParamsForRequest} 
                removeFilterParams={removeSensorFilterParamsForRequest}
                setCardRefreshTimer={setCardRefreshTimer}
                cardRefreshTimer={cardRefreshTimer}
            />
            { buildCardContainer() }
        </>
    );
}