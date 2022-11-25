import * as React from 'react';
import { CardCurrentSensorReadings } from '../../../Components/Cards/Readings/CardCurrentSensorReadings';
import { CardDataResponseInterface, CardCurrentReadingResponse } from '../../../Response/User/CardData/Interfaces/CardDataResponseInterface';

// import Car

export default function CardCurrentReadingBuilder(cardData: CardDataResponseInterface) {
    return (
        <CardCurrentSensorReadings 
            sensorType={cardData.sensorType}
            sensorName={cardData.sensorName}
            room={cardData.sensorRoom}
            cardIcon={cardData.cardIcon}
            sensorData={cardData.sensorData}
        />
    )
}