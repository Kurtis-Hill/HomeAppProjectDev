import * as React from 'react';
import { StandardCurrentReadingSensorCardDataView } from '../Components/Readings/SensorDataOutput/StandardCurrentReadingSensorCardDataView';
import { BoolCurrentReadingSensorCardDataView } from '../Components/Readings/SensorDataOutput/BoolCurrentReadingSensorCardDataView';
import { SensorTypesEnum } from '../../Sensors/Enum/SensorTypesEnum';

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
                <StandardCurrentReadingSensorCardDataView
                    sensorData={sensorData as CurrentSensorDataTypeStandardCard[]}
                />
            )
        case SensorTypesEnum.GenericMotion:
        case SensorTypesEnum.GenericRelay:
            return (
                <BoolCurrentReadingSensorCardDataView
                    sensorData={sensorData as CurrentSensorDataTypeBoolCard[]}
                />
            )
        default:
            return (
                <StandardCurrentReadingSensorCardDataView
                    sensorData={sensorData as CurrentSensorDataTypeStandardCard[]}
                />
            ) 
    }
}