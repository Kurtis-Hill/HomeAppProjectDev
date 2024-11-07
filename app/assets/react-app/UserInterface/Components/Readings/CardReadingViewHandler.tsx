import * as React from 'react';
import {useEffect, useReducer, useState} from 'react';

import {AxiosError, AxiosResponse} from 'axios';
import CardReadingFactory from '../../Factories/CardReadingFactory';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import {CardSensorDataResponseInterface} from '../../Response/Cards/CurrentReadingCardData/CardDataResponseInterface';
import {CurrentSensorDataTypeStandardCard} from '../../Factories/CurrentReadingSensorDataOutputFactory';
import {handleSendingCardDataRequest} from '../../Request/Cards/CardPageRequest';
import {CardFilterBarType} from "../Filterbars/CardFilterBarView";

const initialCardDisplay = [];

const cardReducer = (previousCards: CardSensorDataResponseInterface[]|undefined, cardsForDisplayArray: CardSensorDataResponseInterface[]|undefined) => {
    if (previousCards === undefined || previousCards.length <= 0 ) {
        return cardsForDisplayArray;
    }

    for (let i = 0; i < cardsForDisplayArray.length; i++) {
        const cardForDisplay: CardSensorDataResponseInterface|undefined = cardsForDisplayArray[i];
        if (previousCards[i] === undefined) {
            continue;
        }
        const previousCard: CardSensorDataResponseInterface|undefined = previousCards[i];
        if (cardForDisplay === undefined || previousCard === undefined) {
            continue;
        }
        if (cardForDisplay.sensorData === undefined) {
            continue;
        }
        for (let j = 0; j < cardForDisplay.sensorData.length; j++) {
            if (cardForDisplay.cardViewID === previousCard.cardViewID) {
                const sensorReadingData: CurrentSensorDataTypeStandardCard|undefined = cardForDisplay.sensorData[j];
                const previousSensorReadingData: CurrentSensorDataTypeStandardCard|undefined = previousCard.sensorData[j];
                if ((previousSensorReadingData !== undefined && sensorReadingData !== undefined) && sensorReadingData.readingType === previousSensorReadingData.readingType) {
                    if (sensorReadingData.currentReading !== undefined && previousSensorReadingData.currentReading !== undefined) {
                        if (sensorReadingData.currentReading < previousSensorReadingData.currentReading) {
                            sensorReadingData.lastState = 'down';
                        }
                        if (sensorReadingData.currentReading > previousSensorReadingData.currentReading) {
                            sensorReadingData.lastState = 'up';
                        }
                        if (sensorReadingData.currentReading === previousSensorReadingData.currentReading) {
                            sensorReadingData.lastState = 'same';
                        }
                    }
                }
            }
        }
    }

    return cardsForDisplayArray;
}

export function CardReadingViewHandler(props: { 
    route: string; 
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
    setSelectedCardForQuickUpdate: (cardViewID: number) => void;
    filterParams?: CardFilterBarType; 
    cardRefreshTimer?: number; 
 }) {
    const [loadingCards, setLoadingCards] = useState<boolean>(true);

    const [cardsForDisplay, setCardsForDisplay] = useReducer<CardSensorDataResponseInterface[]>(cardReducer, []);

    const filterParams:CardFilterBarType|[] = props.filterParams ?? {'readingTypes': [], 'sensorTypes': []};

    const cardRefreshTimer = props.cardRefreshTimer

    const route = props.route;

    useEffect(() => {
        handleCardRefresh()
            // .then(() => {
            const interval = setInterval(() => {
                handleCardRefresh();
            }, cardRefreshTimer);

            return () => clearInterval(interval)

            }

    , [filterParams]);
    

    const handleCardRefresh = async () => {
        const cardData: CardSensorDataResponseInterface[] = await handleGettingSensorReadings();
        if (Array.isArray(cardData) && cardData.length > 0) {
            setCardsForDisplay(cardData);
            setLoadingCards(false);
        } else {
            setLoadingCards(false);
            setCardsForDisplay(initialCardDisplay);
        }
    }

    const handleGettingSensorReadings = async (): Promise<Array<CardSensorDataResponseInterface|undefined>> => {
        try {
            const cardDataResponse: AxiosResponse = await handleSendingCardDataRequest({route, filterParams});
            return cardDataResponse.data.payload;
        } catch(error) {
            const err = error as AxiosError|Error;
            return [];
        }
    }

    if (loadingCards === true) {
        return (
            <div className="" style={{ height: "100%", overflow: "hidden", width: "100%", }}>
                <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row hidden-scroll" />
            </div>
        );
    }

    if (cardsForDisplay.length > 0) {
        return (
            <React.Fragment>
                {
                    cardsForDisplay.map((card: CardSensorDataResponseInterface|undefined, index: number) => {
                        return (
                            <React.Fragment key={card?.cardViewID}>
                                {
                                    (card !== undefined)
                                        ?
                                            <div>
                                                <CardReadingFactory
                                                    cardData={card}
                                                    setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                                                    loadingCardModalView={props.loadingCardModalView}
                                                    setLoadingCardModalView={props.setLoadingCardModalView}
                                                />
                                            </div>
                                        :
                                            null
                                }
                            </React.Fragment>
                        )})
                }
            </React.Fragment>
        );
    } else {
        return (
            <>
                <div className="no-data-message">No data to display</div>
            </>
        )
    }
}
