import * as React from 'react';
import { useState, useEffect, useReducer } from 'react';

import { AxiosResponse } from 'axios';
import { AxiosError } from 'axios';
import { CurrentReadingDataDisplayInterface } from './SensorDataOutput/CurrentReadingDataDisplayInterface';

import { CardCurrentSensorDataInterface } from './SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CardFilterBarInterface } from '../Filterbars/CardFilterBarInterface';
import { CardDataResponseInterface } from '../../Response/CardDataResponseInterface';

import { handleSendingCardDataRequest } from '../../../../UserInterface/Cards/Request/CardRequest';
import CardReadingFactory from '../../Factories/CardReadingFactory';
import DotCircleSpinner from '../../../../Common/Components/Spinners/DotCircleSpinner';
import { CardFormResponseInterface } from '../Response/FormResponse/CardFormResponseInterface';

import { CardUpdateModalBuilder } from '../../Builders/CardUpdateModalBuilder'

const cardReducer = (previousCards: React[]|undefined, cardsForDisplayArray: React[]): React[] => {
    if (previousCards.length <= 0 || previousCards === undefined) {
        return cardsForDisplayArray;
    }

    for (let i = 0; i < cardsForDisplayArray.length; i++) {
        const cardForDisplay: CurrentReadingDataDisplayInterface|undefined = cardsForDisplayArray[i].props;
        if (previousCards[i] === undefined) {
            continue;
        }
        const previousCard: CurrentReadingDataDisplayInterface|undefined = previousCards[i].props;
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

export function CardReadingHandler(props: { route: string; filterParams?: CardFilterBarInterface; cardRefreshTimer?: number; }) {
    const [loadingCards, setLoadingCards] = useState<boolean>(true);

    const [cardsForDisplay, setCardsForDisplay] = useReducer(cardReducer, []);

    const route:string = props.route ?? 'index';
    
    const filterParams:CardFilterBarInterface|[] = props.filterParams ?? {'readingTypes': [], 'sensorTypes': []};

    const cardRefreshTimer = props.cardRefreshTimer

    const [cardFormData, setCardFormData] = useState<CardFormResponseInterface>();


    // console.log('filter params in handler', props.filterParams, filterParams)
    useEffect(() => {
        const interval = setInterval(() => {
            handleCardRefresh();
        }, cardRefreshTimer);
        
        return () => clearInterval(interval);
    }, [filterParams, cardRefreshTimer]);
    

    const handleCardRefresh = async () => {
        const cardData: Array<CardDataResponseInterface> = await handleGettingSensorReadings();
        if (Array.isArray(cardData) && cardData.length > 0) {
            prepareCardDataForDisplay(cardData);
            setLoadingCards(false);
        } else {
            setLoadingCards(false);
            setCardsForDisplay(initialCardDisplay);
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
            // cardData[]
            try {
                cardsForDisplayArray.push(            
                    <CardReadingFactory
                      { ...cardData[i] }
                      { ...setCardFormData }
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
                    : cardsForDisplay.length > 0 
                        ? cardsForDisplay.map((card: CardDataResponseInterface|undefined, index: number) => {
                            if (card !== undefined) {
                                return (
                                    <React.Fragment key={index}>
                                        { card }
                                    </React.Fragment>
                                )
                            }
                        })
                        : <div className="no-data-message">No data to display</div>
            }
            {
                <CardUpdateModalBuilder 
                    cardFormData={cardFormData}
                >

                </CardUpdateModalBuilder>
            }
        </React.Fragment>
    );
}