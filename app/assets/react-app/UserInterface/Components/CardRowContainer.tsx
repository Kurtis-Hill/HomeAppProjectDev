import * as React from 'react';
import { useState, useEffect } from 'react';

import { CardReadingViewHandler,  } from './Readings/CardReadingViewHandler';
import { sensorType, readingType } from '../../Sensors/SensorLanguage'

import CardFilterBarView, { CardFilterBarType } from './Filterbars/CardFilterBarView';
import { UpdateCardDisplayModal } from './Modal/UpdateCardDisplayModal';

export function CardRowContainer(props: { 
    route?: string;
    filterParams?: CardFilterBarType;
    horizontal?: boolean; 
    classes?: string; 
}) {
    const { filterParams, route, horizontal, classes } = props;

    const [cardRefreshTimer, setCardRefreshTimer] = useState<number>(4000)

    const [sensorFilterParams, setSensorFilterParams] = useState<CardFilterBarType>(filterParams ?? {readingTypes: [], sensorTypes: []});

    const [selectedCardForQuickUpdate, setSelectedCardForQuickUpdate] = useState<number|null>(null);

    const [loadingCardModalView, setLoadingCardModalView] = useState<boolean>(false);

    const [cardFilterSettingsForceReset, setCardFilterSettingsForceReset] = useState<boolean>(false);

    const forceResetCardFilterSettings = (setting: boolean): void => {
        if (setting === true) {
            setCardFilterSettingsForceReset(true);
        }
        setCardFilterSettingsForceReset(false);
    }

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
                setSensorFilterParams({
                    ...sensorFilterParams, 
                    readingTypes: readingTypes.filter((readingType: string) => {
                        readingType !== filterParam.value
                    })
                });
            }
        }
        if (filterParam.type === sensorType) {
            const sensorTypes: string[] = sensorFilterParams.sensorTypes;
            if (Array.isArray(sensorTypes) && sensorTypes.includes(filterParam.value, sensorFilterParams)) {
                setSensorFilterParams({
                    ...sensorFilterParams, 
                    sensorTypes: sensorTypes.filter((sensorType: string) => {
                        sensorType !== filterParam.value
                    })
                });
            }
        }
    };

    return (
        <>
            <CardFilterBarView 
                filterParams={sensorFilterParams} 
                addFilterParams={addSensorFilterParamsForRequest} 
                removeFilterParams={removeSensorFilterParamsForRequest}
                setCardRefreshTimer={setCardRefreshTimer}
                cardRefreshTimer={cardRefreshTimer}
                setCardFilterSettingsForceReset={forceResetCardFilterSettings}
            />

            {
                horizontal === true
                    ? <div className={classes ?? 'col-xl-12 col-md-12 mb-12'}>
                        <CardReadingViewHandler 
                            route={route}
                            filterParams={sensorFilterParams} 
                            cardRefreshTimer={cardRefreshTimer}
                            setSelectedCardForQuickUpdate={setSelectedCardForQuickUpdate} 
                            loadingCardModalView={loadingCardModalView}
                            setLoadingCardModalView={setLoadingCardModalView}
                        />
                    </div>  
                    :   <CardReadingViewHandler 
                            route={route}
                            filterParams={sensorFilterParams} 
                            cardRefreshTimer={cardRefreshTimer}
                            setSelectedCardForQuickUpdate={setSelectedCardForQuickUpdate} 
                            loadingCardModalView={loadingCardModalView}
                            setLoadingCardModalView={setLoadingCardModalView}
                        />
            }
            {
                loadingCardModalView === true
                    ?
                        <UpdateCardDisplayModal
                            cardViewID={selectedCardForQuickUpdate}
                            loadingCardModalView={loadingCardModalView}
                            setLoadingCardModalView={setLoadingCardModalView}
                        />
                    :
                        null
            }
        </>
    );
}
