import * as React from 'react';
import { useMemo, useState } from 'react';

import { CardReadingHandler } from './Readings/CardReadingHandler';
import { CardFilterBarInterface } from '../Filterbars/CardFilterBarInterface';
import CardFilterBar from '../Filterbars/CardFilterBar';


export function CardRowContainer(props: { route?: string; filterParams?: CardFilterBarInterface; horizontal?: boolean; classes?: string; }) {
    const route: string = props.route ?? 'index';

    const [sensorFilterParams, setSensorFilterParams] = useState<CardFilterBarInterface>(props.filterParams ?? {readingTypes: [], sensorTypes: []});

    const horizontal: boolean = props.horizontal ?? false;
    const classes: string = props.classes;

    const addSensorFilterParamsForRequest = (filterParam: {type: string, value: string}): void => {
        // console.log('sensor filter params', sensorFilterParams);
        
        if (filterParam.type === 'readingType') {
            console.log('setting sensor filter params', filterParam);
            setSensorFilterParams({readingTypes: [filterParam.value]});
            console.log('filter params set', sensorFilterParams);
        }
    };

    const removeSensorFilterParamsForRequest = (filterParam: {type: string, value: string}): void => {
        console.log('setting sensor filter params', filterParam);
        console.log('sensor filter params', sensorFilterParams);

        if (filterParam.type === 'readingType') {
            // console.log('here at least', sensorFilterParams.readingTypes.includes(filterParam.value, sensorFilterParams))
            // if (sensorFilterParams.readingTypes.includes(filterParam.value, sensorFilterParams)) {
            //     console.log('it inculdes it')
            // }
        }
        // setSensorFilterParams({...sensorFilterParams, readingTypes.push(filterParam.value))});
        // console.log('filter params set', sensorFilterParams);
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
            <CardReadingHandler route={route} filterParams={sensorFilterParams} />
        );
    }

    return (
        <>
            <CardFilterBar filterParams={sensorFilterParams} addFilterParams={addSensorFilterParamsForRequest} removeFilterParams={removeSensorFilterParamsForRequest} />
            { buildCardContainer() }
        </>
    );
}