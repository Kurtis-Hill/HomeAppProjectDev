import * as React from 'react';
import { SensorTypesEnum } from '../../../../../Enum/SensorTypesEnum';
import { StandardCurrentReadingSensorDataOutput } from '../Components/Readings/SensorDataOutput/StandardCurrentReadingSensorDataOutput';
import { BoolCurrentReadingSensorDataOutput } from '../Components/Readings/SensorDataOutput/BoolCurrentReadingSensorDataOutput';

export interface CurrentSensorDataTypeStandardCard {
    currentReading: number;
    hightReading: number;
    lowReading: number;
    readingSymbol?: string|null;
    readingType: string;
    updatedAt: Date;
    lastState?: string;
}

export interface CurrentSensorDataTypeBoolCard {
    currentReading: boolean;
    expectedReading: boolean;
    requestedReading: boolean;
    readingType: string;
    updatedAt: Date;
    readingSymbol?: string|null;
}

export function CurrentReadingSensorDataOutputFactory(props: { 
    sensorData: CurrentSensorDataTypeStandardCard[]|CurrentSensorDataTypeBoolCard[], 
    sensorType: SensorTypesEnum 
}) {
    const { sensorData, sensorType } = props;
    
    switch(sensorType) {
        case SensorTypesEnum.Dht:
        case SensorTypesEnum.Dallas:
        case SensorTypesEnum.Bmp:
        case SensorTypesEnum.Soil:
        case SensorTypesEnum.LDR:
        case SensorTypesEnum.Sht:
            return (
                <StandardCurrentReadingSensorDataOutput
                    sensorData={sensorData as CurrentSensorDataTypeStandardCard[]}
                />
            )
        case SensorTypesEnum.GenericMotion:
        case SensorTypesEnum.GenericRelay:
            return (
                <BoolCurrentReadingSensorDataOutput
                    sensorData={sensorData as CurrentSensorDataTypeBoolCard[]}
                />
            )
        default:
            return (
                <StandardCurrentReadingSensorDataOutput
                    sensorData={sensorData as CurrentSensorDataTypeStandardCard[]}
                />
            ) 
    }
}