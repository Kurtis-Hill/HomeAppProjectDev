export interface CurrentReadingDataDisplayInterface {
    cardType: string;
    cardColour: string,
    cardIcon: string,
    cardViewID: number,
    sensorName: string,
    sensorRoom: string,
    sensorType: string,
    sensorData: Array<StandardCardCurrentSensorDataInterface|BoolCardCurrentSensorDataInterface>
}

export interface StandardCardCurrentSensorDataInterface {
    currentReading: number;
    hightReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updatedAt: Date;
    lastState?: string;
}

export interface BoolCardCurrentSensorDataInterface {
    currentReading: boolean;
    expectedReading: boolean;
    requestedReading: boolean;
    readingType: string;
    updatedAt: Date;
    readingSymbol?: string|null;
}