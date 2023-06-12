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
import { updateSensorRequest } from '../Request/Sensor/UpdateSensorRequest';
import { AnnouncementFlashModal } from '../../Common/Components/Modals/AnnouncementFlashModal';
import { AnnouncementFlashModalBuilder } from '../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import CardViewResponseInterface from '../../UserInterface/Cards/Response/CardView/CardViewResponseInterface';


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

    const [showCardModal, setShowCardModal] = useState<boolean>(false);

    const [cardModalLoading, setCardModalLoading] = useState<boolean>(false);

    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

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

        const updatedSensorResponse = await updateSensorRequest(sensor.sensorID, dataToSend);

        if (updatedSensorResponse.status === 202) {
            const updatedSensor: SensorResponseInterface = updatedSensorResponse.data.payload;

            setSensorUpdateFormInputs({
                ...sensorUpdateFormInputs,
                [name]: updatedSensor[name],
            });

            originalSensorData.current = {
                ...originalSensorData.current,
                [name]: updatedSensor[name],
            };

            setActiveFormForUpdating({
                ...activeFormForUpdating,
                [name]: !activeFormForUpdating[name],
            });
        } else {
            showAnnouncementFlash(['Unexpected Response'], 'Error Updating Sensor');
        }    
    }

    const showAnnouncementFlash = (message: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={message}
                timer={timer ? timer : 40}
            />
        ])
    }

    const canEdit: boolean = sensor.canEdit ?? false;
    const canDelete: boolean = sensor.canDelete ?? false;
    const cardView: CardViewResponseInterface = sensor.cardView;
    
    const handleCardViewModal = (cardView: CardViewResponseInterface|null): void => {
        if (cardView === null) {
        }
        setShowCardModal(true)
    }
    return (
        <>
        {
            cardModalLoading === true
                ? <DotCircleSpinner />
                : null
        }
            {
                announcementModals.map((announcementModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            { announcementModal }
                        </React.Fragment>
                    );
                })
            }
            <GeneralTable>
                <GeneralTableHeaders
                    headers={[
                        'Sensor Name',
                        'Sensor Type',
                        'Created By',
                        'User Card',
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
                    <GeneralTableRow>
                        <i onClick={() => handleCardViewModal(cardView)} className={`fas fa-${cardView ? 'check' : 'times'} hover`}></i>
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

            {/* {
                showCardModal === true
                    ?
                        
            } */}
        </>
    );
}