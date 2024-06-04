import * as React from 'react';
import { CardSensorDataResponseInterface } from '../../Response/CurrentReadingCardData/CardDataResponseInterface';
import { StandardCardCurrentSensorReadings } from '../../Components/DisplayCards/StandardCardCurrentSensorReadings';
import { CurrentReadingDataDisplayInterface } from '../../Components/Readings/SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CardReadingFactoryInterface } from '../../Factories/CardReadingFactory';

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