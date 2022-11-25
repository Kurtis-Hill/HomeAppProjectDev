import * as React from 'react';

import CardCurrentReadingBuilder from "../Builders/CardBuilders/ReadingBuilders/CardCurrentReadingBuilder"
import { CardDataResponseInterface } from '../Response/User/CardData/Interfaces/CardDataResponseInterface';

export default function CardReadingFactory(props: CardDataResponseInterface) {
    const cardType: string = props.cardType;

    switch (cardType) {
        case 'current-reading':
            return <CardCurrentReadingBuilder
                { ...props }
            />;
        default:
            return <CardCurrentReadingBuilder 
                { ...props }
            />;
    }
}