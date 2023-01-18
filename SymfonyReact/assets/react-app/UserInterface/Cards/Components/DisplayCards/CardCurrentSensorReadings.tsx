import * as React from 'react';
import { BaseCard } from '../BaseCard';
import { CardCurrentSensorDataInterface } from '../Readings/SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CurrentReadingSensorDataOutput } from '../Readings/SensorDataOutput/CurrentReadingSensorDataOutput';

import { DisplayCardRequestContainer } from './DisplayCardRequestContainer';
// import { CardCurrentReadingResponse } from '../../Response/CardDataResponseInterface';

export function CardCurrentSensorReadings(props: {
    cardViewID: number;
    sensorType: string;
    sensorName: string; 
    room: string; 
    sensorData: CardCurrentSensorDataInterface[];
    cardIcon: string; 
    cardColour?: string|undefined
}): React {
    const cardViewID: number = props.cardViewID;
    const sensorType: string = props.sensorType;
    const sensorName: string = props.sensorName;
    const sensorRoom: string = props.room;
    const cardIcon: string = props.cardIcon ?? 'dog';
    const cardColour: string|undefined = props.cardColour;

    const sensorData: CardCurrentSensorDataInterface[] = props.sensorData;

    return (
        <DisplayCardRequestContainer cardViewID={cardViewID}>
            <BaseCard colour={cardColour} cardClasses=''>
                <React.Fragment>
                    <div className="card-sensor-type-display-name">Type: <b>{sensorType}</b></div>
                    <div className="row no-gutters align-items-center">
                        <div className="col mr-2">
                            <div className="d-flex font-weight-bold text text-uppercase mb-1">Name: {sensorName}</div>
                            <div className="d-flex text text-uppercase mb-1 card-room-text-display">Area: {sensorRoom}</div>
                                <CurrentReadingSensorDataOutput
                                    sensorData={sensorData}
                                />
                        </div>
                        <div className="col-auto">
                            <i className={`fas fa-2x text-gray-300 fa-${cardIcon}`}></i>
                        </div>
                    </div>
                </React.Fragment>                
            </BaseCard>
        </DisplayCardRequestContainer>
    );
}