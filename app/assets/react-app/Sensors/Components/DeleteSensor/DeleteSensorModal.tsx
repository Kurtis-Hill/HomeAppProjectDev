import * as React from 'react';
import { useState } from 'react';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import BaseModal from '../../../Common/Components/Modals/BaseModal';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { deleteSensorRequest } from '../../Request/Sensor/DeleteSensorRequest';

export function DeleteSensorModal(props: {
    sensorID: number,
    sensorName: string,
    refreshData: () => void,
}) {
    const { sensorID, sensorName, refreshData } = props;

    const [showDeleteSensorModal, setShowDeleteSensorModal] = useState<boolean>(false)

    const [announcementModals, setAnnouncementModals] = useState<JSX.Element<Array<typeof AnnouncementFlashModal>>>([]);

    const deleteSensorHandler = async (e: Event) => {
        e.preventDefault();
        const sensorDeletedRequestPayload = await deleteSensorRequest(sensorID);

        if (sensorDeletedRequestPayload !== null) {
            setShowDeleteSensorModal(false);

            setAnnouncementModals([
                <AnnouncementFlashModalBuilder
                    title={`Success`}
                    dataToList={[`Sensor ${sensorName} deleted successfully`]}
                    setAnnouncementModals={setAnnouncementModals}
                    timer={40}
                />
            ]);

            setTimeout(() => {
                refreshData();                
            }, 2000);
        } else {
            setAnnouncementModals([
                <AnnouncementFlashModalBuilder
                    title={`Error`}
                    dataToList={[`Sensor ${sensorName} could not be deleted`]}
                    setAnnouncementModals={setAnnouncementModals}
                    timer={40}
                />
            ]);
        }
    }

    return (
        <>
            {
                announcementModals.map((announcementModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            { announcementModal }
                        </React.Fragment>
                    );
                })

            }
            <button type="delete" onClick={() => setShowDeleteSensorModal(true)} style={{ borderRadius: "5px" }} ><i className="fas fa-trash delete fa-fw"></i></button>
            {
                showDeleteSensorModal === true
                    ?
                        <BaseModal
                            title={`Delete Sensor`}
                            modalShow={true}
                            setShowModal={setShowDeleteSensorModal}
                        >
                        <p>Are you sure you want to delete sensor {sensorName}?</p>
                        <SubmitButton
                                    type="submit"
                                    text='Delete Sensor'
                                    name='delete-sensor'
                                    action='submit'
                                    classes='add-new-submit-button'
                                    onClickFunction={deleteSensorHandler}
                        />
                        <CloseButton 
                            close={setShowDeleteSensorModal} 
                            classes={"modal-cancel-button"} 
                        />
                        </BaseModal>
                    :
                        null
            }
            </>
    );
}
