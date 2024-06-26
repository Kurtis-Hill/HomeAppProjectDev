import * as React from 'react';

import { CurrentReadingDataDisplayFactory } from './CurrentReadingDataDisplayFactory';
import { CardSensorDataResponseInterface } from '../Response/Cards/CurrentReadingCardData/CardDataResponseInterface';

export default function CardReadingFactory(props:
    CardReadingFactoryInterface 
): React {
    const cardType: string = props.cardData.cardType;

    switch (cardType) {
        case 'current-reading':
            return (
                <CurrentReadingDataDisplayFactory 
                    cardData={props.cardData}
                    setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                    loadingCardModalView={props.loadingCardModalView}
                    setLoadingCardModalView={props.setLoadingCardModalView}
                />
            )
        default:
            return ( 
                <CurrentReadingDataDisplayFactory 
                    cardData={props.cardData}
                    setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                    loadingCardModalView={props.loadingCardModalView}
                    setLoadingCardModalView={props.setLoadingCardModalView}
                />
            )
    }
}

export interface CardReadingFactoryInterface {
    cardData: CardSensorDataResponseInterface;
    setSelectedCardForQuickUpdate: (cardViewID: number) => void;
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
}