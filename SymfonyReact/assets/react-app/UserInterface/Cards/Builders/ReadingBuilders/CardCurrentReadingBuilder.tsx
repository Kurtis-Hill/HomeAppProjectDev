import * as React from 'react';
import { CardDataResponseInterface } from '../../Response/CardDataResponseInterface';
import { CardCurrentSensorReadings } from '../../Components/DisplayCards/CardCurrentSensorReadings';
import { CurrentReadingDataDisplayInterface } from '../../Components/Readings/SensorDataOutput/CurrentReadingDataDisplayInterface';


export default function CardCurrentReadingBuilder(cardData: CardDataResponseInterface|CurrentReadingDataDisplayInterface, setCardFormData: (cardFormData: any) => void) {
    return (
        <CardCurrentSensorReadings
            cardViewID={cardData.cardViewID}
            sensorType={cardData.sensorType}
            sensorName={cardData.sensorName}
            room={cardData.sensorRoom}
            cardIcon={cardData.cardIcon}
            cardColour={cardData.cardColour}
            sensorData={cardData.sensorData}
        />
    )
}