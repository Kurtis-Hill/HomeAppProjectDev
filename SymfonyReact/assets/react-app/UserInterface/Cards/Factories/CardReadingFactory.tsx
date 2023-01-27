import * as React from 'react';

import CardCurrentReadingBuilder from "../Builders/ReadingBuilders/CardCurrentReadingBuilder"
import { CardDataResponseInterface } from '../Response/CardDataResponseInterface';

export default function CardReadingFactory(props:
    CardReadingFactoryInterface 
): React {
    const cardType: string = props.cardData.cardType;

    switch (cardType) {
        case 'current-reading':
            return (
                <CardCurrentReadingBuilder
                    cardData={props.cardData}
                    setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                    loadingCardModalView={props.loadingCardModalView}
                    setLoadingCardModalView={props.setLoadingCardModalView}
                />
            )
        default:
            return ( 
                <CardCurrentReadingBuilder 
                    cardData={props.cardData}
                    setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                    loadingCardModalView={props.loadingCardModalView}
                    setLoadingCardModalView={props.setLoadingCardModalView}
                />
            )
    }
}

export interface CardReadingFactoryInterface {
    cardData: CardDataResponseInterface;
    setSelectedCardForQuickUpdate: (cardViewID: number) => void;
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
}