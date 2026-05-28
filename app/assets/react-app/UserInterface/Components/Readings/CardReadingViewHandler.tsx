import * as React from 'react';
import {useEffect, useReducer, useState} from 'react';

import { AxiosResponse } from 'axios';
import CardReadingFactory from '../../Factories/CardReadingFactory';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import {CardSensorDataResponseInterface} from '../../Response/Cards/CurrentReadingCardData/CardDataResponseInterface';
import {CurrentSensorDataTypeStandardCard} from '../../Factories/CurrentReadingSensorDataOutputFactory';
import {handleSendingCardDataRequest} from '../../Request/Cards/CardPageRequest';
import {CardFilterBarType} from "../Filterbars/CardFilterBarView";

const initialCardDisplay: CardSensorDataResponseInterface[] = [];

const cardReducer = (
    previousCards: CardSensorDataResponseInterface[] | undefined,
    cardsForDisplayArray: CardSensorDataResponseInterface[] | undefined,
) => {
    if (previousCards === undefined || previousCards.length <= 0) {
        return cardsForDisplayArray;
    }
    for (let i = 0; i < cardsForDisplayArray.length; i++) {
        const cardForDisplay = cardsForDisplayArray[i];
        if (previousCards[i] === undefined) continue;
        const previousCard = previousCards[i];
        if (cardForDisplay === undefined || previousCard === undefined) continue;
        if (cardForDisplay.sensorData === undefined) continue;
        for (let j = 0; j < cardForDisplay.sensorData.length; j++) {
            if (cardForDisplay.cardViewID === previousCard.cardViewID) {
                const srd  = cardForDisplay.sensorData[j] as CurrentSensorDataTypeStandardCard;
                const psrd = previousCard.sensorData[j]    as CurrentSensorDataTypeStandardCard;
                if (srd && psrd && srd.readingType === psrd.readingType) {
                    if (srd.currentReading !== undefined && psrd.currentReading !== undefined) {
                        srd.lastState = srd.currentReading < psrd.currentReading ? 'down'
                            : srd.currentReading > psrd.currentReading ? 'up'
                            : 'same';
                    }
                }
            }
        }
    }
    return cardsForDisplayArray;
};

export function CardReadingViewHandler(props: {
    route: string;
    loadingCardModalView: boolean;
    setLoadingCardModalView: (v: boolean) => void;
    setSelectedCardForQuickUpdate: (cardViewID: number) => void;
    filterParams?: CardFilterBarType;
    cardRefreshTimer?: number;
    onCountChange?: (count: number) => void;
}) {
    const [loadingCards, setLoadingCards] = useState<boolean>(true);
    const [cardsForDisplay, setCardsForDisplay] = useReducer(cardReducer, [] as CardSensorDataResponseInterface[]);

    const filterParams: CardFilterBarType | [] = props.filterParams ?? { readingTypes: [], sensorTypes: [] };
    const { cardRefreshTimer, route, onCountChange } = props;

    useEffect(() => {
        handleCardRefresh();
        const interval = setInterval(handleCardRefresh, cardRefreshTimer);
        return () => clearInterval(interval);
    }, [filterParams, route, cardRefreshTimer]);

    const handleCardRefresh = async () => {
        const cardData = await handleGettingSensorReadings();
        if (Array.isArray(cardData) && cardData.length > 0) {
            setCardsForDisplay(cardData);
            onCountChange?.(cardData.length);
            setLoadingCards(false);
        } else {
            setLoadingCards(false);
            setCardsForDisplay(initialCardDisplay);
            onCountChange?.(0);
        }
    };

    const handleGettingSensorReadings = async (): Promise<CardSensorDataResponseInterface[]> => {
        try {
            const res: AxiosResponse = await handleSendingCardDataRequest({ route, filterParams });
            return res.data.payload;
        } catch {
            return [];
        }
    };

    if (loadingCards) {
        return (
            <div className="row">
                <div className="col-12 text-center py-5">
                    <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row" />
                    <p className="text-muted mt-3 small">Loading sensor cards…</p>
                </div>
            </div>
        );
    }

    if (cardsForDisplay.length > 0) {
        return (
            <div className="row">
                {cardsForDisplay.map((card: CardSensorDataResponseInterface | undefined) => (
                    <React.Fragment key={card?.cardViewID}>
                        {card !== undefined && (
                            <CardReadingFactory
                                cardData={card}
                                setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                                loadingCardModalView={props.loadingCardModalView}
                                setLoadingCardModalView={props.setLoadingCardModalView}
                            />
                        )}
                    </React.Fragment>
                ))}
            </div>
        );
    }

    return (
        <div className="row">
            <div className="col-12">
                <div className="card shadow mb-4">
                    <div className="card-body text-center py-5 text-muted">
                        <i className="fas fa-satellite-dish fa-3x d-block mb-3 text-gray-300" />
                        <h5 className="font-weight-bold">No sensor cards to display</h5>
                        <p className="mb-0 small">Try adjusting your filters, or check that the sensors are online and assigned to your account.</p>
                    </div>
                </div>
            </div>
        </div>
    );
}
