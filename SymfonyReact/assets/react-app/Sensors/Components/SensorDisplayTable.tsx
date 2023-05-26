import * as React from 'react';
import { useState, useEffect, useRef } from 'react';

import { GeneralTable } from '../../Common/Components/Table/General/GeneralTable';
import { GeneralTableHeaders } from '../../Common/Components/Table/General/GeneralTableHeaders';
import { GeneralTableBody } from '../../Common/Components/Table/General/GeneralTableBody';
import { GeneralTableRow } from '../../Common/Components/Table/General/GeneralTableRow';
import SensorResponseInterface from '../Response/Sensor/SensorResponseInterface';
import { FormInlineInput } from '../../Common/Components/Inputs/FormInlineUpdate';
import { SensorPatchRequestInputInterface } from '../Response/Sensor/SensorPatchRequestInputInterface';
import { DeleteSensor } from './DeleteSensor/DeleteSensor';

export function SensorDisplayTable(props: {sensor: SensorResponseInterface, refreshData?: () => void,}) {
    const { sensor, refreshData } = props;

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
        sensorName: false,
        sensorType: false,
        device: false,
        createdBy: false,
        expandSensor: false,
    });

    const originalSensorData = useRef<SensorResponseInterface>({
        sensorName: sensor.sensorName,
        sensorType: sensor.sensorType,
        device: sensor.device,
        createdBy: sensor.createdBy,
    });

    const [sensorUpdateFormInputs, setSensorUpdateFormInputs] = useState<SensorResponseInterface>({
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

        let dataToSend: SensorPatchRequestInputInterface = {};

        switch (name) {
            case 'sensorName':
                dataToSend.sensorName = sensorUpdateFormInputs.sensorName;
                break;
            case 'deviceName':
                dataToSend.deviceName = sensorUpdateFormInputs.device.deviceName;
                break;
        }
    }
    
    const canEdit: boolean = sensor.canEdit ?? false;
    const canDelete: boolean = sensor.canDelete ?? false;
    
    return (
        <>
            <GeneralTable>
                <GeneralTableHeaders
                    headers={[
                        'Sensor Name',
                        'Sensor Type',
                        'Created By',
                        canDelete === true ? 'Delete' : '',
                    ]}
                />
                <GeneralTableBody>
                    <GeneralTableRow>
                        {
                            activeFormForUpdating.sensorName === true && canEdit === true
                                ?
                                    <FormInlineInput
                                        changeEvent={handleUpdateSensorInput}
                                        nameParam='sensorName'
                                        value={sensorUpdateFormInputs.sensorName}
                                        dataName='sensorName' 
                                        acceptClickEvent={(e: Event) => sendUpdateSensorRequest(e)}
                                        declineClickEvent={(e: Event) => toggleFormInput(e)}
                                        extraClasses='center-text'
                                        />
                                        
                                :                
                                    <span className={`${canEdit === true ? 'hover' : ''}`} data-name="sensorName" onClick={(e: Event) => toggleFormInput(e)}>{sensor.sensorName}</span>
                            }
                    </GeneralTableRow>
                    <GeneralTableRow>
                        <span>{sensor?.sensorType?.sensorTypeName}</span>
                    </GeneralTableRow>
                    <GeneralTableRow>
                        <span>{sensor?.createdBy?.email}</span>
                    </GeneralTableRow>                            
                    {         
                        canDelete === true
                            ?
                                <GeneralTableRow>
                                    <DeleteSensor
                                        sensorID={sensor.sensorID}
                                        sensorName={sensor.sensorName}
                                        refreshData={refreshData}
                                    />
                                </GeneralTableRow>
                            : 
                            null            
                    }
                    
                </GeneralTableBody>
            </GeneralTable>       

        </>
    );
}