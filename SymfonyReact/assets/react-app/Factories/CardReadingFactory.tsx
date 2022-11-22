import * as React from 'react';

import CardCurrentReadingBuilder from "../Builders/CardBuilders/ReadingBuilders/CardCurrentReadingBuilder"

export default function CardReadingFactory(props: {cardType: string}) {
    const cardType: string = props.cardType;

    switch (cardType) {
        case 'current-reading':
            return <CardCurrentReadingBuilder />;
        default:
            return <CardCurrentReadingBuilder />;
    }
}