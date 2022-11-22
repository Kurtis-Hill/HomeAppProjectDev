export interface CardDataResponseInterface {
    cardColour: string;
    cardIcon: string;
    cardViewID: number;
    sensorName: string;
    sensorRoom: string;
    sensorType: string;
    sensorData: Array<CardCurrentReadingResponse>
}

export interface CardCurrentReadingResponse {
    currentReading: number;
    hightReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updateAt: Date;
}