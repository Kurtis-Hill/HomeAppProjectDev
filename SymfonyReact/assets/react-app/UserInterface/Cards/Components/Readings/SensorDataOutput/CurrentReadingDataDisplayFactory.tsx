import * as React from 'react';
import { SensorTypesEnum } from '../../../../../Enum/SensorTypesEnum';
import * as ReactDOM from 'react-dom/client';
import { CardReadingFactoryInterface } from '../../../Factories/CardReadingFactory';
import CardCurrentReadingBuilder from '../../../Builders/ReadingBuilders/CardCurrentReadingBuilder';

export function CurrentReadingDataDisplayFactory(props: CardReadingFactoryInterface): React.ReactNode {
    const readingType: SensorTypesEnum = props.cardData.sensorType;

    switch (readingType) {
        case SensorTypesEnum.Dht:
        case SensorTypesEnum.Bmp:
        case SensorTypesEnum.Soil:
        case SensorTypesEnum.Dallas:
        case SensorTypesEnum.GenericMotion:
        case SensorTypesEnum.GenericRelay:
            return (
                <CardCurrentReadingBuilder
                    cardData={props.cardData}
                    setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                    loadingCardModalView={props.loadingCardModalView}
                    setLoadingCardModalView={props.setLoadingCardModalView}
                />
            );
    }
}