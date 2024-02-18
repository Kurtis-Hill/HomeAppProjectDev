import * as React from 'react';
import SensorResponseInterface from '../Response/Sensor/SensorResponseInterface';
import { UpdateSingleSensorCard } from '../Components/SensorUpdate/UpdateSingleSensorCard';
import { AddNewSensorButton } from '../Components/AddSensor/AddNewSensorButton';

export function ViewSensorsPage(props: {
    sensorData: SensorResponseInterface[],
    refreshData?: () => void,
}) {
    const { sensorData, refreshData } = props;

    if (sensorData.length === 0) {
        return (
            <h1>No Sensors to Display</h1>
        )
    }

    return (
        <>
            {
                sensorData.map((sensor: SensorResponseInterface, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            <UpdateSingleSensorCard sensor={sensor} refreshData={refreshData}  />
                        </React.Fragment>
                    );
                })
            }
            <AddNewSensorButton deviceID={sensorData[0].device.deviceID} refreshData={refreshData}/>
        </>
    )
}