export interface CurrentReadingDataDisplayInterface {
    cardType: string;
    cardColour: string,
    cardIcon: string,
    cardViewID: number,
    sensorName: string,
    sensorRoom: string,
    sensorType: string,
    sensorData: Array<CardCurrentSensorDataInterface>
}

export interface CardCurrentSensorDataInterface {
    currentReading: number;
    hightReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updatedAt: Date;
    lastState?: string;
}