import * as React from 'react';
import { useState, useEffect, useReducer } from 'react';

import { AxiosResponse } from 'axios';
import { AxiosError } from 'axios';

import { handleSendingCardDataRequest } from '../../../Request/CardRequest';

import { CardDataResponseInterface } from '../../../Response/User/CardData/Interfaces/CardDataResponseInterface';

import CardReadingFactory from '../../../Factories/CardReadingFactory';
import DotCircleSpinner from '../../Spinners/DotCircleSpinner';

import { CardCurrentSensorDataInterface, CurrentReadingDataDisplayInterface } from './SensorDataOutput/CurrentReadingDataDisplayInterface';

const cardReducer = (previousCards: React[], cardsForDisplayArray: React[]) => {
    if (previousCards.length <= 0) {
        return cardsForDisplayArray;
    }

    for (let i = 0; i < cardsForDisplayArray.length; i++) {
        const cardForDisplay: CurrentReadingDataDisplayInterface = cardsForDisplayArray[i].props;
        const previousCard: CurrentReadingDataDisplayInterface = previousCards[i].props;
        for (let j = 0; j < cardForDisplay.sensorData.length; j++) {
            const sensorReadingData: CardCurrentSensorDataInterface = cardForDisplay.sensorData[j];
            const previousSensorReadingData: CardCurrentSensorDataInterface = previousCard.sensorData[j];

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

    return cardsForDisplayArray;
}

const initialCardState = [];

export function CardReadingHandler(props: { route: string; filterParams?: string[] }) {
    const route:string = props.route ?? 'index';
    const filterParams:string[] = props.filterParams ?? [];

    const [loadingCards, setLoadingCards] = useState<boolean>(true);
    const [refreshTimer, setRefreshTimer] = useState<number>(4000);
    const [cardsForDisplay, setCardsForDisplay] = useReducer(cardReducer, initialCardState);

    useEffect(() => {
        const interval = setInterval(() => {
            handleCardRefresh();
        }, refreshTimer);
        
        return () => clearInterval(interval);
    }, []);
    

    const handleCardRefresh = async () => {
        const cardData: Array<CardDataResponseInterface> = await handleGettingSensorReadings();
        if (cardData.length > 0) {
            prepareCardDataForDisplay(cardData);
            setLoadingCards(false);
        } else {
            setLoadingCards(true);
        }
    }

    const handleGettingSensorReadings = async (): Promise<Array<CardDataResponseInterface|undefined>> => {
        try {
            const cardDataResponse: AxiosResponse = await handleSendingCardDataRequest({route, filterParams});
            const cardData: Array<CardDataResponseInterface> = cardDataResponse.data.payload;

            return cardData;
        } catch(error) {
            const err = error as AxiosError|Error;
            return [];           
        }
    }
    
    const prepareCardDataForDisplay = (cardData: Array<CardDataResponseInterface|undefined>): void => {
        let cardsForDisplayArray: Array<React> = [];
        for (let i = 0; i < cardData.length; i++) {
            try {
                cardsForDisplayArray.push(            
                    <CardReadingFactory
                        { ...cardData[i] }
                    />
                );
            } catch(err) {
                console.log('could not build card', err);
            }            
        }

        setCardsForDisplay(cardsForDisplayArray);
    }

    return (
        <React.Fragment>
            {            
                loadingCards === true 
                    ? <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row" />
                    : cardsForDisplay.map((card: CardDataResponseInterface|undefined, index: number) => {
                        if (card !== undefined) {
                            return (
                                <React.Fragment key={index}>
                                    {card}
                                </React.Fragment>
                            )
                        }
                    })
            }
        </React.Fragment>
    );
}