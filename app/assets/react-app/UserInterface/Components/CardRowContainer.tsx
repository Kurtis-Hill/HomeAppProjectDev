import * as React from 'react';
import { useState } from 'react';

import { CardReadingViewHandler } from './Readings/CardReadingViewHandler';
import { sensorType, readingType } from '../../Sensors/SensorLanguage'

import CardFilterBarView, { CardFilterBarType } from './Filterbars/CardFilterBarView';
import { UpdateCardDisplayModal } from './Modal/UpdateCardDisplayModal';

export function CardRowContainer(props: { 
    route?: string;
    filterParams?: CardFilterBarType;
    horizontal?: boolean; 
    classes?: string; 
}) {
    const { filterParams, route } = props;

    const [cardRefreshTimer, setCardRefreshTimer] = useState<number>(4000);
    const [sensorFilterParams, setSensorFilterParams] = useState<CardFilterBarType>(filterParams ?? {readingTypes: [], sensorTypes: []});
    const [selectedCardForQuickUpdate, setSelectedCardForQuickUpdate] = useState<number|null>(null);
    const [loadingCardModalView, setLoadingCardModalView] = useState<boolean>(false);
    const [cardFilterSettingsForceReset, setCardFilterSettingsForceReset] = useState<boolean>(false);
    const [cardsCount, setCardsCount] = useState<number>(0);

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
                    readingTypes: readingTypes.filter((rt: string) => rt !== filterParam.value)
                });
            }
        }
        if (filterParam.type === sensorType) {
            const sensorTypes: string[] = sensorFilterParams.sensorTypes;
            if (Array.isArray(sensorTypes) && sensorTypes.includes(filterParam.value, sensorFilterParams)) {
                setSensorFilterParams({
                    ...sensorFilterParams, 
                    sensorTypes: sensorTypes.filter((st: string) => st !== filterParam.value)
                });
            }
        }
    };

    const refreshSeconds = cardRefreshTimer / 1000;

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

            {/* Status bar */}
            <div className="d-flex align-items-center justify-content-between mb-3">
                <small className="text-muted">
                    <i className="fas fa-th-large mr-1" />
                    {cardsCount > 0
                        ? <><strong>{cardsCount}</strong> sensor card{cardsCount !== 1 ? 's' : ''} loaded</>
                        : 'Loading cards…'}
                </small>
                <small className="text-muted">
                    <i className="fas fa-sync-alt mr-1" />Refresh every <strong>{refreshSeconds}s</strong>
                </small>
            </div>

            <CardReadingViewHandler
                route={route}
                filterParams={sensorFilterParams}
                cardRefreshTimer={cardRefreshTimer}
                setSelectedCardForQuickUpdate={setSelectedCardForQuickUpdate}
                loadingCardModalView={loadingCardModalView}
                setLoadingCardModalView={setLoadingCardModalView}
                onCountChange={setCardsCount}
            />

            {loadingCardModalView === true && (
                <UpdateCardDisplayModal
                    cardViewID={selectedCardForQuickUpdate}
                    loadingCardModalView={loadingCardModalView}
                    setLoadingCardModalView={setLoadingCardModalView}
                />
            )}
        </>
    );
}
