import * as React from 'react';
import {useState, useRef} from 'react';

import SensorResponseInterface from '../Response/Sensor/SensorResponseInterface';
import { FormInlineInput } from '../../Common/Components/Inputs/FormInlineUpdate';
import { SensorPatchRequestInputInterface } from '../Response/Sensor/SensorPatchRequestInputInterface';
import { DeleteSensorModal } from './DeleteSensor/DeleteSensorModal';
import { updateSensorRequest } from '../Request/Sensor/UpdateSensorRequest';
import { AnnouncementFlashModalBuilder } from '../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import BaseModal from '../../Common/Components/Modals/BaseModal';
import CloseButton from '../../Common/Components/Buttons/CloseButton';
import CardViewResponseInterface from '../../UserInterface/Response/Cards/CardView/CardViewResponseInterface';
import { AddNewCardTypeInterface, addNewCardRequest } from '../../UserInterface/Request/Cards/Card/AddNewCardRequest';
import { UpdateCard } from '../../UserInterface/Components/Form/UpdateCard';
import { getSensorTypeColour } from '../Enum/SensorTypeColours';

const defaultFormActiveState = {
    sensorName: false,
    sensorType: false,
    device: false,
    createdBy: false,
    expandSensor: false,
    pinNumber: false,
    readingInterval: false,
};

export function SensorDisplayTable(props: { sensor: SensorResponseInterface; refreshData?: () => void }) {
    const { sensor, refreshData } = props;

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({ ...defaultFormActiveState });

    const originalSensorData = useRef<SensorResponseInterface>({ ...sensor });

    const [sensorUpdateFormInputs, setSensorUpdateFormInputs] = useState<SensorResponseInterface>({ ...sensor });

    const [updateCardView, setUpdateCardView] = useState<CardViewResponseInterface | null>(null);
    const [showUpdateCardModal, setShowUpdateCardModal] = useState<boolean>(false);
    const [createCardLoading, setCreateCardLoading] = useState<boolean>(false);
    const [announcementModals, setAnnouncementModals] = useState<React.JSX.Element[]>([]);

    const toggleFormInput = (name: string) => {
        setActiveFormForUpdating(prev => ({ ...prev, [name]: !prev[name] }));
        setSensorUpdateFormInputs(prev => ({ ...prev, [name]: originalSensorData.current[name] }));
    };

    const handleUpdateSensorInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;
        setSensorUpdateFormInputs(prev => ({ ...prev, [name]: value }));
    };

    const sendUpdateSensorRequest = async (name: string) => {
        let dataToSend: SensorPatchRequestInputInterface = {};
        switch (name) {
            case 'sensorName':
                dataToSend.sensorName = sensorUpdateFormInputs.sensorName;
                break;
            case 'deviceName':
                dataToSend.deviceName = sensorUpdateFormInputs.device.deviceName;
                break;
            case 'pinNumber':
                dataToSend.pinNumber = parseInt(String(sensorUpdateFormInputs.pinNumber));
                break;
            case 'readingInterval':
                dataToSend.readingInterval = parseInt(String(sensorUpdateFormInputs.readingInterval));
                break;
        }

        const updatedSensorResponse = await updateSensorRequest(sensor.sensorID, dataToSend);
        if (updatedSensorResponse.status === 200 || updatedSensorResponse.status === 202) {
            const updatedSensor: SensorResponseInterface = updatedSensorResponse.data.payload;
            setSensorUpdateFormInputs(prev => ({ ...prev, [name]: updatedSensor[name] }));
            originalSensorData.current = { ...originalSensorData.current, [name]: updatedSensor[name] };
            setActiveFormForUpdating({ ...defaultFormActiveState });
        } else {
            showAnnouncementFlash(['Unexpected Response'], 'Error Updating Sensor');
        }
    };

    const showAnnouncementFlash = (message: string[], title: string, timer?: number | null) => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={message}
                timer={timer ?? 40}
            />,
        ]);
    };

    const canEdit: boolean = sensor.canEdit ?? false;
    const canDelete: boolean = sensor.canDelete ?? false;
    const cardView: CardViewResponseInterface = sensor.cardView;

    const handleCardViewModal = async (cardView: CardViewResponseInterface | null) => {
        if (cardView === null) {
            setCreateCardLoading(true);
            const addNewCardResponse = await addNewCardRequest({ sensorID: sensor.sensorID } as AddNewCardTypeInterface);
            setCreateCardLoading(false);
            if (addNewCardResponse.status === 200 && refreshData) refreshData();
        } else {
            setUpdateCardView(cardView);
            setShowUpdateCardModal(true);
        }
    };

    return (
        <>
            {announcementModals.map((modal, i) => <React.Fragment key={i}>{modal}</React.Fragment>)}

            {showUpdateCardModal && (
                <BaseModal modalShow={showUpdateCardModal} title="User Card Update" setShowModal={setShowUpdateCardModal}>
                    <UpdateCard cardViewID={updateCardView.cardViewID} />
                    <CloseButton close={setShowUpdateCardModal} classes="modal-cancel-button" />
                </BaseModal>
            )}

            {/* Card header — editable sensor name + delete */}
            <div className="sensor-card-header">
                <div className="sensor-card-name-edit">
                    {activeFormForUpdating.sensorName && canEdit ? (
                        <FormInlineInput
                            changeEvent={handleUpdateSensorInput}
                            nameParam="sensorName"
                            value={sensorUpdateFormInputs.sensorName}
                            dataName="sensorName"
                            acceptClickEvent={() => sendUpdateSensorRequest('sensorName')}
                            declineClickEvent={() => toggleFormInput('sensorName')}
                            extraClasses="center-text"
                        />
                    ) : (
                        <h5
                            className={`sensor-card-name${canEdit ? ' hover' : ''}`}
                            onClick={() => canEdit && toggleFormInput('sensorName')}
                            title={canEdit ? 'Click to edit name' : undefined}
                        >
                            {originalSensorData.current.sensorName}
                        </h5>
                    )}
                </div>
                <div className="sensor-card-actions">
                    {canDelete && (
                        <DeleteSensorModal
                            sensorID={sensor.sensorID}
                            sensorName={sensor.sensorName}
                            refreshData={refreshData}
                        />
                    )}
                </div>
            </div>

            {/* Card body — info grid */}
            <div className="sensor-card-body">
                <div className="sensor-info-grid">

                    {/* Pin Number */}
                    <span className="sensor-info-label">Pin</span>
                    <span className="sensor-info-value">
                        {activeFormForUpdating.pinNumber && canEdit ? (
                            <FormInlineInput
                                changeEvent={handleUpdateSensorInput}
                                nameParam="pinNumber"
                                value={sensorUpdateFormInputs.pinNumber}
                                dataName="pinNumber"
                                acceptClickEvent={() => sendUpdateSensorRequest('pinNumber')}
                                declineClickEvent={() => toggleFormInput('pinNumber')}
                                extraClasses="center-text"
                            />
                        ) : (
                            <span
                                className={canEdit ? 'hover' : ''}
                                onClick={() => canEdit && toggleFormInput('pinNumber')}
                                title={canEdit ? 'Click to edit' : undefined}
                            >
                                {originalSensorData.current.sensorReadingTypes?.analog !== undefined ? 'A' : ''}
                                {originalSensorData.current.pinNumber}
                            </span>
                        )}
                    </span>

                    {/* Reading Interval */}
                    <span className="sensor-info-label">Interval</span>
                    <span className="sensor-info-value">
                        {activeFormForUpdating.readingInterval && canEdit ? (
                            <FormInlineInput
                                changeEvent={handleUpdateSensorInput}
                                nameParam="readingInterval"
                                value={sensorUpdateFormInputs.readingInterval}
                                dataName="readingInterval"
                                acceptClickEvent={() => sendUpdateSensorRequest('readingInterval')}
                                declineClickEvent={() => toggleFormInput('readingInterval')}
                                extraClasses="center-text"
                            />
                        ) : (
                            <span
                                className={canEdit ? 'hover' : ''}
                                onClick={() => canEdit && toggleFormInput('readingInterval')}
                                title={canEdit ? 'Click to edit' : undefined}
                            >
                                {originalSensorData.current.readingInterval} ms
                            </span>
                        )}
                    </span>

                    {/* Sensor Type */}
                    <span className="sensor-info-label">Type</span>
                    <span className="sensor-info-value">
                        {sensor?.sensorType?.sensorTypeName
                            ? (() => {
                                const typeName = sensor.sensorType.sensorTypeName;
                                const colour   = getSensorTypeColour(typeName);
                                return (
                                    <span
                                        className="badge badge-pill"
                                        style={{ background: colour, color: '#fff', fontSize: '0.7rem', letterSpacing: '0.04em' }}
                                    >
                                        {typeName}
                                    </span>
                                );
                              })()
                            : '—'}
                    </span>

                    {/* Created By */}
                    <span className="sensor-info-label">Created by</span>
                    <span className="sensor-info-value">{sensor?.createdBy?.email ?? '—'}</span>

                    {/* Card View */}
                    <span className="sensor-info-label">Dashboard card</span>
                    <span className="sensor-info-value">
                        {createCardLoading ? (
                            <DotCircleSpinner spinnerSize={1} />
                        ) : (
                            <i
                                className={`fas fa-${cardView ? 'check-circle' : 'times-circle'} sensor-card-view-icon ${cardView ? 'has-card' : 'no-card'}`}
                                onClick={() => handleCardViewModal(cardView)}
                                title={cardView ? 'Update dashboard card' : 'Add to dashboard'}
                            />
                        )}
                    </span>

                </div>
            </div>
        </>
    );
}
