import * as React from 'react';
import { CardCurrentSensorReadings } from '../../../Components/Cards/Readings/CardCurrentSensorReadings';
import { CurrentReadingDataDisplayInterface } from '../../../Components/Cards/Readings/SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CardDataResponseInterface } from '../../../Response/User/CardData/Interfaces/CardDataResponseInterface';

export default function CardCurrentReadingBuilder(cardData: CardDataResponseInterface|CurrentReadingDataDisplayInterface) {
    return (
        <CardCurrentSensorReadings 
            sensorType={cardData.sensorType}
            sensorName={cardData.sensorName}
            room={cardData.sensorRoom}
            cardIcon={cardData.cardIcon}
            cardColour={cardData.cardColour}
            sensorData={cardData.sensorData}
        />
    )
}