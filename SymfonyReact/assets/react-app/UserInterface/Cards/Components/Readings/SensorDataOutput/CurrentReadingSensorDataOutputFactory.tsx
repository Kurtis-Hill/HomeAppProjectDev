import * as React from 'react';
import { BoolCardCurrentSensorDataInterface, StandardCardCurrentSensorDataInterface } from './CurrentReadingDataDisplayInterface';
import { SensorTypesEnum } from '../../../../../Enum/SensorTypesEnum';
import { StandardCurrentReadingSensorDataOutput } from './StandardCurrentReadingSensorDataOutput';
import { BoolCurrentReadingSensorDataOutput } from './BoolCurrentReadingSensorDataOutput';

export function CurrentReadingSensorDataOutputFactory(props: { sensorData: StandardCardCurrentSensorDataInterface[]|BoolCardCurrentSensorDataInterface[], sensorType: SensorTypesEnum }) {
    const { sensorData, sensorType } = props;
    
    switch(sensorType) {
        case SensorTypesEnum.Dht:
        case SensorTypesEnum.Dallas:
        case SensorTypesEnum.Bmp:
        case SensorTypesEnum.Soil:
            return (
                <StandardCurrentReadingSensorDataOutput
                    sensorData={sensorData as StandardCardCurrentSensorDataInterface[]}
                />
            )
        case SensorTypesEnum.GenericMotion:
        case SensorTypesEnum.GenericRelay:
            return (
                <BoolCurrentReadingSensorDataOutput
                    sensorData={sensorData as BoolCardCurrentSensorDataInterface[]}
                />
            )
        default:
            return (
                <StandardCurrentReadingSensorDataOutput
                    sensorData={sensorData as StandardCardCurrentSensorDataInterface[]}
                />
            ) 
    }
}