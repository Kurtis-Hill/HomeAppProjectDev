import * as React from "react";
import SensorResponseInterface from '../Response/Sensor/SensorResponseInterface';

export default function QueryView() {
    const [sensorList, setSensorList] = React.useState<SensorResponseInterface[]|[]>([]);
    

    return (
        <div>
            <h1>Query Builder</h1>
            <h2>Select Sensor to query</h2>
            <select>
                {sensorList.map((sensor: SensorResponseInterface) => {
                    return (
                        <option key={sensor.sensorID} value={sensor.sensorID}>{sensor.sensorName}</option>
                    )
                })}
            </select>
        </div>
    );
}