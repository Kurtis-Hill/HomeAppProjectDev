import * as React from 'react';
import { CardSensorDataResponseInterface } from '../../Cards/Response/CurrentReadingCardData/CardDataResponseInterface';
import { StandardCardCurrentSensorReadings } from '../../Cards/Components/DisplayCards/StandardCardCurrentSensorReadings';
import { CurrentReadingDataDisplayInterface } from '../../Cards/Components/Readings/SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CardReadingFactoryInterface } from '../../Cards/Factories/CardReadingFactory';

export default function CardCurrentReadingBuilder(
    props: CardReadingFactoryInterface
): React.ReactElement {
    const cardData: CardSensorDataResponseInterface = props.cardData;

    
    return (
        <StandardCardCurrentSensorReadings
            cardViewID={cardData.cardViewID}
            sensorType={cardData.sensorType}
            sensorName={cardData.sensorName}
            room={cardData.sensorRoom}
            cardIcon={cardData.cardIcon}
            cardColour={cardData.cardColour}
            sensorData={cardData.sensorData}
            setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
            loadingCardModalView={props.loadingCardModalView}
            setLoadingCardModalView={props.setLoadingCardModalView}
        />
    )
}