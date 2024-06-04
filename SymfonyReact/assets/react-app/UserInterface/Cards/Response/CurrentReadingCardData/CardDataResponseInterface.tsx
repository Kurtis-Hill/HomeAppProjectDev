import { SensorTypesEnum } from '../../../../Enum/SensorTypesEnum';

export interface CardSensorDataResponseInterface {
    cardType: string;
    cardColour: string,
    cardIcon: string,
    cardViewID: number,
    sensorName: string,
    sensorRoom: string,
    sensorType: SensorTypesEnum,
    sensorData: Array<CurrentCardCurrentReadingResponse|BoolCurrentReadingResponse>
}

export interface CurrentCardCurrentReadingResponse {
    currentReading: number;
    hightReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updatedAt: Date;
}

export interface BoolCurrentReadingResponse {
    currentReading: number;
    hightReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updatedAt: Date;
}