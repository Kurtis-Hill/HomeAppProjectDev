import * as React from 'react';
import { useState } from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorDisplayTable } from '../SensorDisplayTable';
import { ReadingTypeDisplayTable } from '../ReadingTypes/ReadingTypeDisplayTable';

export function UpdateSingleSensorCard(props: {sensor: SensorResponseInterface, refreshData?: () => void,}) {
    const { sensor, refreshData } = props;

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
        sensorName: false,
        sensorType: false,
        device: false,
        createdBy: false,
        expandSensor: false,
    });

    const toggleDisplay = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;

        setActiveFormForUpdating({
            ...activeFormForUpdating,
            [name]: !activeFormForUpdating[name],
        });
    }


    return (
        <>
            <div className="container" style={{ paddingBottom: "5%" }}>
                <div className="card" style={{ margin: "inherit", border: 'solid' }}>
                    <div className="card-body"> 
                        <SensorDisplayTable sensor={sensor} refreshData={refreshData} />
                        <div style={{paddingTop: "3%"}}>
                            {
                                activeFormForUpdating.expandSensor === true
                                    ?
                                        <>  
                                            <ReadingTypeDisplayTable sensorReadingTypes={sensor.sensorReadingTypes} canEdit={sensor.canEdit} refreshData={refreshData} />
                                        </>
                                    : 
                                        null
                            }
                        </div>
                        <i onClick={(e: Event) => {toggleDisplay(e)}} data-name="expandSensor" className={`fas fa-${activeFormForUpdating.expandSensor === true ? 'minus' : 'plus' } hover edit fa-fw`}></i>
                    </div>
                </div>
            </div>
        </>
    );
}