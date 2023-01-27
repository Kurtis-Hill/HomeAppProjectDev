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
import { CardDisplayModal } from '../Modal/CardDisplayModal';

import { CardCurrentSensorReadings } from '../DisplayCards/CardCurrentSensorReadings';

const cardReducer = (previousCards: CardDataResponseInterface[]|undefined, cardsForDisplayArray: CardDataResponseInterface[]|undefined): React[] => {
    if (previousCards.length <= 0 || previousCards === undefined) {
        return cardsForDisplayArray;
    }

    for (let i = 0; i < cardsForDisplayArray.length; i++) {
        const cardForDisplay: CardDataResponseInterface|undefined = cardsForDisplayArray[i];
        if (previousCards[i] === undefined) {
            continue;
        }
        const previousCard: CardDataResponseInterface|undefined = previousCards[i];
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

    const [cardsForDisplay, setCardsForDisplay] = useReducer<CardDataResponseInterface[]>(cardReducer, []);

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
        const cardData: Array<CardDataResponseInterface> = await handleGettingSensorReadings();
        if (Array.isArray(cardData) && cardData.length > 0) {
            // prepareCardDataForDisplay(cardData);
            console.log('card data request recieved')
            setCardsForDisplay(cardData);
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