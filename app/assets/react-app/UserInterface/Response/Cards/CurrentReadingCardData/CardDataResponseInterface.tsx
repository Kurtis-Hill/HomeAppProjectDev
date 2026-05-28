import { SensorTypesEnum } from "../../../../Sensors/Enum/SensorTypesEnum";

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
    highReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updatedAt: string;
}

export interface BoolCurrentReadingResponse {
    currentReading: number;
    highReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updatedAt: string;
}
