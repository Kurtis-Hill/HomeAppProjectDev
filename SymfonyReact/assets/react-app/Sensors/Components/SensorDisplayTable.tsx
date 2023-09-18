import * as React from 'react';
import { useState, useRef } from 'react';

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
import { UpdateCard } from '../../UserInterface/Cards/Components/Form/UpdateCard';
import BaseModal from '../../Common/Components/Modals/BaseModal';
import CloseButton from '../../Common/Components/Buttons/CloseButton';
import { addNewCardRequest, AddNewCardType } from '../../UserInterface/Cards/Request/Card/AddNewCardRequest';
import {SensorTypesEnum} from "../../Enum/SensorTypesEnum";

const defaultFormActiveState = {
    sensorName: false,
    sensorType: false,
    device: false,
    createdBy: false,
    expandSensor: false,
    pinNumber: false,
    readingInterval: false,
};

export function SensorDisplayTable(props: {sensor: SensorResponseInterface, refreshData?: () => void,}) {
    const { sensor, refreshData } = props;

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
        defaultFormActiveState,
    });

    const originalSensorData = useRef<SensorResponseInterface>({
        sensorName: sensor.sensorName,
        pinNumber: sensor.pinNumber,
        sensorType: sensor.sensorType,
        device: sensor.device,
        createdBy: sensor.createdBy,
        readingInterval: sensor.readingInterval,
        canDelete: sensor.canDelete,
        canEdit: sensor.canEdit,
        cardView: sensor.cardView,
        sensorID: sensor.sensorID,
        sensorReadingTypes: sensor.sensorReadingTypes,
    });

    const [sensorUpdateFormInputs, setSensorUpdateFormInputs] = useState<SensorResponseInterface>({
        sensorName: sensor.sensorName,
        pinNumber: sensor.pinNumber,
        sensorType: sensor.sensorType,
        device: sensor.device,
        createdBy: sensor.createdBy,
        readingInterval: sensor.readingInterval,
        canDelete: sensor.canDelete,
        canEdit: sensor.canEdit,
        cardView: sensor.cardView,
        sensorID: sensor.sensorID,
        sensorReadingTypes: sensor.sensorReadingTypes,
    });

    const [updateCardView, setUpdateCardView] = useState<CardViewResponseInterface|null>(null);

    const [showUpdateCardModal, setShowUpdateCardModal] = useState<boolean>(false);

    const [createCardLoading, setCreateCardLoading] = useState<boolean>(false);

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

        console.log('sensorUpdateFormInputs', name, value)
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
            case 'pinNumber':
                dataToSend.pinNumber = parseInt(sensorUpdateFormInputs.pinNumber);
                break;
            case 'readingInterval':
                dataToSend.readingInterval = parseInt(sensorUpdateFormInputs.readingInterval);
                break;
        }

        const updatedSensorResponse = await updateSensorRequest(sensor.sensorID, dataToSend);

        if (updatedSensorResponse.status === 200 || updatedSensorResponse.status === 202) {
            console.log('updated sensor', dataToSend);
            const updatedSensor: SensorResponseInterface = updatedSensorResponse.data.payload;

            setSensorUpdateFormInputs({
                ...sensorUpdateFormInputs,
                [name]: updatedSensor[name],
            });

            originalSensorData.current = {
                ...originalSensorData.current,
                [name]: updatedSensor[name],
            };

            setActiveFormForUpdating(defaultFormActiveState);
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
    
    const handleCardViewModal = async (cardView: CardViewResponseInterface|null) => {
        if (cardView === null) {
            setCreateCardLoading(true);
            const addNewCardData: AddNewCardType = {
                sensorID: sensor.sensorID,
            };

            const addNewCardResponse = await addNewCardRequest(addNewCardData);

            if (addNewCardResponse.status === 200) {
                setCreateCardLoading(false);
                if (refreshData !== undefined) {
                    refreshData();
                }
            } else {
                setCreateCardLoading(false);
            }
        } else {
            setUpdateCardView(cardView);
            setShowUpdateCardModal(true);
        }
    }

    return (
        <>
            {
                showUpdateCardModal === true
                    ? 
                        <BaseModal modalShow={showUpdateCardModal} title='User Card Update' setShowModal={setShowUpdateCardModal}>
                            <UpdateCard cardViewID={updateCardView.cardViewID} />
                            <CloseButton 
                                close={setShowUpdateCardModal} 
                                classes={"modal-cancel-button"} 
                            />
                        </BaseModal>
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
                        'Pin Number',
                        'Reading Interval (ms)',
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
                                    <span className={`${canEdit === true ? 'hover' : ''}`} data-name="sensorName" onClick={(e: Event) => toggleFormInput(e)}>{originalSensorData.current.sensorName}</span>
                            }
                    </GeneralTableRow>
                    <GeneralTableRow>
                        {
                            activeFormForUpdating.pinNumber === true && canEdit === true
                                ?
                                    <FormInlineInput
                                        changeEvent={handleUpdateSensorInput}
                                        nameParam='pinNumber'
                                        value={sensorUpdateFormInputs.pinNumber}
                                        dataName='pinNumber'
                                        acceptClickEvent={(e: Event) => sendUpdateSensorRequest(e)}
                                        declineClickEvent={(e: Event) => toggleFormInput(e)}
                                        extraClasses='center-text'
                                    />
                                :
                                    <span className={`${canEdit === true ? 'hover' : ''}`} data-name="pinNumber" onClick={(e: Event) => toggleFormInput(e)}>{originalSensorData.current.sensorReadingTypes.analog !== undefined  ? 'A' : '' }{originalSensorData.current.pinNumber}</span>
                        }                            
                    </GeneralTableRow>
                    <GeneralTableRow>
                        {
                            activeFormForUpdating.readingInterval === true && canEdit === true
                                ?
                                    <FormInlineInput
                                            changeEvent={handleUpdateSensorInput}
                                            nameParam='readingInterval'
                                            value={sensorUpdateFormInputs.readingInterval}
                                            dataName='readingInterval'
                                            acceptClickEvent={(e: Event) => sendUpdateSensorRequest(e)}
                                            declineClickEvent={(e: Event) => toggleFormInput(e)}
                                            extraClasses='center-text'
                                        />
                                    :
                                        <span className={`${canEdit === true ? 'hover' : ''}`} data-name="readingInterval" onClick={(e: Event) => toggleFormInput(e)}>{originalSensorData.current.readingInterval}</span>
                        }
                    </GeneralTableRow>
                    <GeneralTableRow>
                        <span>{sensor?.sensorType?.sensorTypeName}</span>
                    </GeneralTableRow>
                    <GeneralTableRow>
                        <span>{sensor?.createdBy?.email}</span>
                    </GeneralTableRow>                            
                    <GeneralTableRow>
                        { createCardLoading === false ? <i onClick={() => handleCardViewModal(cardView)} className={`fas fa-${cardView ? 'check' : 'times'} hover`}></i> : <DotCircleSpinner classes='center-spinner-absolute' spinnerSize={2} />}
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
