import * as React from 'react';

import CardCurrentReadingBuilder from "../Builders/ReadingBuilders/CardCurrentReadingBuilder"
import { CardDataResponseInterface } from '../Response/CardDataResponseInterface';

export default function CardReadingFactory(props: CardDataResponseInterface, setCardFormData: (cardFormData: any) => void): React {
    const cardType: string = props.cardType;

    switch (cardType) {
        case 'current-reading':
            return <CardCurrentReadingBuilder
                 { ...props }
                 setCardFormData={setCardFormData} 
                 />;
        default:
            return <CardCurrentReadingBuilder 
            { ...props }
            setCardFormData={setCardFormData} 
            />;
    }
}

// interface CardReadingFactoryInterface {
//     cardData: CardDataResponseInterface;
//     setCardFormData: (cardFormData: any) => void;
// }