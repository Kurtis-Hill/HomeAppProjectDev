import * as React from 'react';
import { CardCurrentReadingResponse } from '../../../Response/User/CardData/Interfaces/CardDataResponseInterface';
import { BaseCard } from '../BaseCard';
import { CardCurrentSensorDataInterface, CurrentReadingDataDisplayInterface } from './SensorDataOutput/CurrentReadingDataDisplayInterface';
import { CurrentReadingSensorDataOutput } from './SensorDataOutput/CurrentReadingSensorDataOutput';

export function CardCurrentSensorReadings(props: {
    sensorType: string;
    sensorName: string; 
    room: string; 
    sensorData: CardCurrentSensorDataInterface[]|CardCurrentSensorDataInterface[];
    cardIcon: string; 
    cardColour?: string|undefined
}) {
    const sensorType: string = props.sensorType;
    const sensorName: string = props.sensorName;
    const sensorRoom: string = props.room;
    const cardIcon: string = props.cardIcon ?? 'dog';
    const cardColour: string|undefined = props.cardColour;

    const sensorData: CardCurrentSensorDataInterface[] = props.sensorData;

    return (
        <BaseCard
            content={
                <React.Fragment>
                    <div style={{ position: "absolute", top: '2%', right: '5%'}}>Type: <b>{sensorType}</b></div>
                    <div className="row no-gutters align-items-center">
                        <div className="col mr-2">
                            <div className="d-flex font-weight-bold text text-uppercase mb-1">Name: {sensorName}</div>
                            <div style={{paddingBottom: '2%'}} className="d-flex text text-uppercase mb-1">Area: {sensorRoom}</div>
                                <CurrentReadingSensorDataOutput
                                    sensorData={sensorData}
                                />
                        </div>
                        <div className="col-auto">
                            <i className={`fas fa-2x text-gray-300 fa-${cardIcon}`}></i>
                        </div>
                    </div>
                </React.Fragment>                
            }
            colour={cardColour}
        />
    );
}