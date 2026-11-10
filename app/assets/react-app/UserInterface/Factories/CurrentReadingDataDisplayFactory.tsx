import * as React from 'react';
import { CardReadingFactoryInterface } from './CardReadingFactory';
import { SensorTypesEnum } from '../../Sensors/Enum/SensorTypesEnum';
import CardCurrentReadingBuilder from '../Builders/ReadingBuilders/CardCurrentReadingBuilder';
import { BoolCurrentSensorReadingsCardView } from '../Components/DisplayCards/BoolCurrentSensorReadingsCardView';

export function CurrentReadingDataDisplayFactory(props: CardReadingFactoryInterface): React.ReactNode {
    const readingType: SensorTypesEnum = props.cardData.sensorType;

    switch (readingType) {
        case SensorTypesEnum.GenericMotion:
        case SensorTypesEnum.GenericRelay:
            return (
                <BoolCurrentSensorReadingsCardView
                    cardViewID={props.cardData.cardViewID}
                    sensorType={props.cardData.sensorType}
                    sensorName={props.cardData.sensorName}
                    room={props.cardData.sensorRoom}
                    cardIcon={props.cardData.cardIcon}
                    cardColour={props.cardData.cardColour}
                    sensorData={props.cardData.sensorData as any}
                    setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
                    loadingCardModalView={props.loadingCardModalView}
                    setLoadingCardModalView={props.setLoadingCardModalView}
                />
            );

        case SensorTypesEnum.Dht:
        case SensorTypesEnum.Bmp:
        case SensorTypesEnum.Soil:
        case SensorTypesEnum.Dallas:
        case SensorTypesEnum.LDR:
        case SensorTypesEnum.Sht:
        default:
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
