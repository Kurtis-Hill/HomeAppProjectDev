import * as React from 'react';
import SensorResponseInterface from '../Response/Sensor/SensorResponseInterface';
import { UpdateSingleSensorCard } from '../Components/SensorUpdate/UpdateSingleSensorCard';
import { AddNewSensorModal } from '../Components/AddSensor/AddNewSensorModal';

export function SensorsView(props: {
    sensorData: SensorResponseInterface[],
    deviceID: number,
    refreshData?: () => void,
}) {
    const { sensorData, refreshData, deviceID } = props;

    return (
        <>
            <div className="sensor-list-toolbar">
                <span className="sensor-list-toolbar-title">
                    <i className="fas fa-microchip mr-2" />
                    Sensors
                    {sensorData.length > 0 && (
                        <span className="sensor-list-count">{sensorData.length}</span>
                    )}
                </span>
                <AddNewSensorModal deviceID={deviceID} refreshData={refreshData} />
            </div>
            {sensorData.length === 0 ? (
                <p className="no-data-message">No sensors found for this device.</p>
            ) : (
                <div className="sensor-list">
                    {sensorData.map((sensor: SensorResponseInterface, index: number) => (
                        <React.Fragment key={index}>
                            <UpdateSingleSensorCard sensor={sensor} refreshData={refreshData} />
                        </React.Fragment>
                    ))}
                </div>
            )}
        </>
    );
}
