import * as React from 'react';
import { useState, useEffect, useMemo } from 'react';

import { CardReadingHandler } from './Readings/CardReadingHandler';
import { CardFilterBarInterface } from './Filterbars/CardFilterBarInterface'; 
import { sensorType, readingType } from '../../../Common/SensorLanguage'
import CardFilterBar from './Filterbars/CardFilterBar';
import { CardDisplayModal } from './Modal/CardDisplayModal';


export function CardRowContainer(props: { 
    route?: string;
    filterParams?: CardFilterBarInterface;
    horizontal?: boolean; 
    classes?: string; 
}) {
    const { filterParams, route, horizontal, classes } = props;
    
    const [cardRefreshTimer, setCardRefreshTimer] = useState<number>(4000)
    
    const [sensorFilterParams, setSensorFilterParams] = useState<CardFilterBarInterface>(filterParams ?? {readingTypes: [], sensorTypes: []});

    const [selectedCardForQuickUpdate, setSelectedCardForQuickUpdate] = useState<number|null>(null);

    const [loadingCardModalView, setLoadingCardModalView] = useState<boolean>(false);
        
    const addSensorFilterParamsForRequest = (filterParam: {type: string, value: string}): void => {
        const filterParamType = filterParam.type;
        
        if (filterParamType === readingType) {
            setSensorFilterParams({...sensorFilterParams, readingTypes: [...sensorFilterParams.readingTypes, filterParam.value]});
        }
        if (filterParamType === sensorType) {
            setSensorFilterParams({...sensorFilterParams, sensorTypes: [...sensorFilterParams.sensorTypes, filterParam.value]});
        }
    };

    const removeSensorFilterParamsForRequest = (filterParam: {type: string, value: string}): void => {
        if (filterParam.type === readingType) {
            const readingTypes: string[] = sensorFilterParams.readingTypes;
            if (Array.isArray(readingTypes) && readingTypes.includes(filterParam.value, sensorFilterParams)) {
                setSensorFilterParams({...sensorFilterParams, readingTypes: readingTypes.filter(readingType => readingType !== filterParam.value)});
            }
        }
        if (filterParam.type === sensorType) {
            const sensorTypes: string[] = sensorFilterParams.sensorTypes;
            if (Array.isArray(sensorTypes) && sensorTypes.includes(filterParam.value, sensorFilterParams)) {
                setSensorFilterParams({...sensorFilterParams, sensorTypes: sensorTypes.filter((sensorType: string) => sensorType !== filterParam.value)});
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
                setSelectedCardForQuickUpdate={setSelectedCardForQuickUpdate} 
                loadingCardModalView={loadingCardModalView}
                setLoadingCardModalView={setLoadingCardModalView}
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

            {
                horizontal === true
                    ? <div className={classes ?? 'col-xl-12 col-md-12 mb-12'}>
                        <CardReadingHandler 
                            route={route} 
                            filterParams={sensorFilterParams} 
                            cardRefreshTimer={cardRefreshTimer}
                            setSelectedCardForQuickUpdate={setSelectedCardForQuickUpdate} 
                            loadingCardModalView={loadingCardModalView}
                            setLoadingCardModalView={setLoadingCardModalView}
                        />
                    </div>  
                    :   <CardReadingHandler 
                            route={route} 
                            filterParams={sensorFilterParams} 
                            cardRefreshTimer={cardRefreshTimer}
                            setSelectedCardForQuickUpdate={setSelectedCardForQuickUpdate} 
                            loadingCardModalView={loadingCardModalView}
                            setLoadingCardModalView={setLoadingCardModalView}
                        />
            }
            <CardDisplayModal
                cardViewID={selectedCardForQuickUpdate}
                loadingCardModalView={loadingCardModalView}
                setLoadingCardModalView={setLoadingCardModalView}
            />
        </>
    );
}