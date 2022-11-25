import * as React from 'react';
import { useState, useEffect } from 'react';

import { AxiosResponse } from 'axios';
import { AxiosError } from 'axios';

import { handleSendingCardDataRequest } from '../../../Request/CardRequest';

import { CardDataResponseInterface } from '../../../Response/User/CardData/Interfaces/CardDataResponseInterface';

import CardReadingFactory from '../../../Factories/CardReadingFactory';

export function CardReadingHandler(props: { route: string; filterParams?: string[] }) {
    const route:string = props.route ?? 'index';
    const filterParams:string[] = props.filterParams ?? [];
    
    const [refreshTimer, setRefreshTimer] = useState<number>(3000);
    const [cardsForDisplay, setCardsForDisplay] = useState<Array<CardDataResponseInterface>>([]);

    useEffect(() => {
        const interval = setInterval(() => {
            handleCardRefresh();
        }, refreshTimer);
        
        return () => clearInterval(interval);
    }, []);
    

    const handleCardRefresh = async () => {
        try {
            const cardData: Array<CardDataResponseInterface> = await handleGettingSensorReadings(route);
            const preparedCards = prepareCardDataForDisplay(cardData);
            setCardsForDisplay(preparedCards);
        } catch(err) {
            const error = err as AxiosError|Error;
        }
    }

    const handleGettingSensorReadings = async (route: string): Promise<Array<CardDataResponseInterface|undefined>> => {
        try {
            const cardDataResponse: AxiosResponse = await handleSendingCardDataRequest({route});
            const cardData: Array<CardDataResponseInterface> = cardDataResponse.data.payload;
console.log(cardData);
            return cardData;
        } catch(error) {
            const err = error as AxiosError|Error;
            return [];           
        }
    }


    const prepareCardDataForDisplay = (cardData: Array<CardDataResponseInterface|undefined>) => {
        let cardsForDisplay: Array<string> = [];
        for (let i = 0; i < cardData.length; i++) {
            try {
                cardsForDisplay.push(            
                    <CardReadingFactory
                        { ...cardData[i] }
                    />
                );
            } catch(err) {
                console.log('could not build card', err);
            }            
        }

        return cardsForDisplay;
    }


    return (
        <React.Fragment>
            {
                cardsForDisplay.map((card: CardDataResponseInterface|undefined, index: number) => {
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