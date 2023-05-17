import * as React from 'react';
import SensorResponseInterface from '../../Sensor/Response/SensorResponseInterface';
import { useState, useEffect, useRef } from 'react';
import { FormInlineInputWLabel } from '../../../Common/Components/Inputs/FormInlineInputWLabel';

export function UpdateSingleSensorInline(props: {sensor: SensorResponseInterface}) {
    const { sensor } = props;

    const [activeFormForUpdating, setActiveFormForUpdating] = useState<boolean>({
        sensorName: false,
        sensorType: false,
        device: false,
        createdBy: false,
    });

    const [sensorUpdateFormInputs, setSensorUpdateFormInputs] = useState<SensorResponseInterface>({
        sensorName: sensor.sensorName,
        sensorType: sensor.sensorType,
        device: sensor.device,
        createdBy: sensor.createdBy,
    });

    const originalSensorData = useRef<SensorResponseInterface>({
        sensorName: sensor.sensorName,
        sensorType: sensor.sensorType,
        device: sensor.device,
        createdBy: sensor.createdBy,
    });

    const toggleFormInput = (event: Event) => {
        const name = (event.target as HTMLElement|HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement|HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;

        setActiveFormForUpdating({
            ...activeFormForUpdating,
            [name]: !activeFormForUpdating[name],
        });

        setSensorUpdateFormInputs({
            ...sensorUpdateFormInputs,
            [name]: originalSensorData.current[name],
        });
    }

    const handleUpdateSensorInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setSensorUpdateFormInputs({
            ...sensorUpdateFormInputs,
            [name]: value,
        });
    }

    const sendUpdateSensorRequest = async (event: Event) => {
        const name = (event.target as HTMLElement).dataset.name;
        const value = (event.target as HTMLElement).dataset.value;

        let dataToSend: {sensorName?: string, deviceName?: string} = {};

        switch (name) {
            case 'sensorName':
                dataToSend.sensorName = sensorUpdateFormInputs.sensorName;
                break;
            case 'deviceName':
                dataToSend.deviceName = sensorUpdateFormInputs.device.deviceName;
                break;
        }
    }

    return (
        <>
            <div className="card" style={{ margin: "inherit" }}>
                <div className="card-body"> 
                {
                    activeFormForUpdating.sensorName === true
                        ?
                            <FormInlineInputWLabel
                                labelName="Sensor Name: "
                                changeEvent={handleUpdateSensorInput}
                                nameParam='sensorName'
                                value={sensorUpdateFormInputs.sensorName}
                                dataName='sensorName' 
                                acceptClickEvent={(e: Event) => sendUpdateSensorRequest(e)}
                                declineClickEvent={(e: Event) => toggleFormInput(e)}
                            />
                        :                
                            <h5 className="card-title hover" data-name="sensorName" onClick={(e: Event) => toggleFormInput(e)}>{sensor.sensorName}</h5>
                }
                    
                    {/* <h6 className="card-subtitle mb-2 text-muted">{sensor.sensorType.sensorTypeName}</h6> */}
                    {/* <p className="card-text">Device: {sensor.device.deviceName}</p>
                    <p className="card-text">Sensor Type: {sensor.sensorType.sensorTypeName}</p>
                    <p className="card-text">Created By: {sensor.createdBy.email}</p>
                    <a href="#" className="card-link">Card link</a>
                    <a href="#" className="card-link">Another link</a>  */}
                </div>
            </div>
        </>
    );
}