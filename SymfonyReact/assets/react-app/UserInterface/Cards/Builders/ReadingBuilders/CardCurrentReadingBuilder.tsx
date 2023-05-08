import * as React from 'react';
import { CardSensorDataResponseInterface } from '../../Response/CurrentReadingCardData/CardDataResponseInterface';
import { CardCurrentSensorReadings } from '../../Components/DisplayCards/CardCurrentSensorReadings';
import { CurrentReadingDataDisplayInterface } from '../../Components/Readings/SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CardReadingFactoryInterface } from '../../Factories/CardReadingFactory';

export default function CardCurrentReadingBuilder(
    props: CardReadingFactoryInterface
): React.ReactElement {
    console.log('card current reading builders')
    const cardData: CardSensorDataResponseInterface = props.cardData;

    return (
        <CardCurrentSensorReadings
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