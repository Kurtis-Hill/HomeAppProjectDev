import * as React from 'react';
import SensorResponseInterface from '../../Sensor/Response/SensorResponseInterface';
import { UpdateSingleSensorInline } from './UpdateSingleSensorInline';

export function UpdateSensors(props: {
    sensorData: SensorResponseInterface[]
}) {
    const { sensorData } = props;

    if (sensorData.length === 0) {
        return (
            <h1>No Sensors to Display</h1>
        )
    }

    return (
        <>
            {
                sensorData.map((sensor: SensorResponseInterface, index: number) => {
                    console.log('sensorData', sensor);
                    return (
                        <React.Fragment key={index}>
                            <UpdateSingleSensorInline sensor={sensor} />
                        </React.Fragment>
                    );
                })
            }
        </>
    )
}