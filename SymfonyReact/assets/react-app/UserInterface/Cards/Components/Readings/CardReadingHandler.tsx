import * as React from 'react';
import { useState, useEffect, useReducer } from 'react';

import { AxiosResponse } from 'axios';
import { AxiosError } from 'axios';
import { CurrentReadingDataDisplayInterface } from './SensorDataOutput/CurrentReadingDataDisplayInterface';

import { CardCurrentSensorDataInterface } from './SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CardFilterBarInterface } from '../Filterbars/CardFilterBarInterface';
import { CardSensorDataResponseInterface } from '../../Response/CurrentReadingCardData/CardDataResponseInterface';

import { handleSendingCardDataRequest } from '../../../../UserInterface/Cards/Request/CardRequest';
import CardReadingFactory from '../../Factories/CardReadingFactory';
import DotCircleSpinner from '../../../../Common/Components/Spinners/DotCircleSpinner';

const cardReducer = (previousCards: CardSensorDataResponseInterface[]|undefined, cardsForDisplayArray: CardSensorDataResponseInterface[]|undefined): React[] => {
    if (previousCards.length <= 0 || previousCards === undefined) {
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
            if (cardForDisplay !== undefined && cardForDisplay.cardViewID === previousCard.cardViewID) {
                const sensorReadingData: CardCurrentSensorDataInterface|undefined = cardForDisplay.sensorData[j];
                const previousSensorReadingData: CardCurrentSensorDataInterface|undefined = previousCard.sensorData[j];
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

const initialCardDisplay = [];

export function CardReadingHandler(props: { 
    route: string; 
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
    setSelectedCardForQuickUpdate: (cardViewID: number) => void;
    filterParams?: CardFilterBarInterface; 
    cardRefreshTimer?: number; 
 }) {
    const [loadingCards, setLoadingCards] = useState<boolean>(true);

    const [cardsForDisplay, setCardsForDisplay] = useReducer<CardSensorDataResponseInterface[]>(cardReducer, []);

    const route:string = props.route ?? 'index';
    
    const filterParams:CardFilterBarInterface|[] = props.filterParams ?? {'readingTypes': [], 'sensorTypes': []};

    const cardRefreshTimer = props.cardRefreshTimer

    useEffect(() => {
        const interval = setInterval(() => {
            handleCardRefresh();
        }, cardRefreshTimer);
        
        return () => clearInterval(interval);
    }, [filterParams, cardRefreshTimer]);
    

    const handleCardRefresh = async () => {
        const cardData: Array<CardSensorDataResponseInterface> = await handleGettingSensorReadings();
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
            const cardData: Array<CardSensorDataResponseInterface> = cardDataResponse.data.payload;

            return cardData;
        } catch(error) {
            const err = error as AxiosError|Error;
            return [];           
        }
    }

    if (loadingCards === true) {
        return (
            <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row" />
        );
    }
    return (   
        <React.Fragment>
            {            
                cardsForDisplay.length > 0 
                    ? cardsForDisplay.map((card: CardSensorDataResponseInterface|undefined, index: number) => {
                        if (card !== undefined) {
                                    return (
                                        <React.Fragment key={index}>
                                            <CardReadingFactory
                                                cardData={card} 
                                                setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                                                loadingCardModalView={props.loadingCardModalView}
                                                setLoadingCardModalView={props.setLoadingCardModalView}
                                            />
                                        </React.Fragment>
                                    )
                                }
                            })
                    : <div className="no-data-message">No data to display</div>
            }
        </React.Fragment>
    );
}