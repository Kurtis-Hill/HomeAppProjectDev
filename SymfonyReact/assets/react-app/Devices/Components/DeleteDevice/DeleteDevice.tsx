import * as React from 'react';
import { NavigateFunction, useNavigate } from "react-router-dom";
import { useState, useEffect, useRef } from 'react';
import { AxiosResponse } from 'axios';
import BaseModal from '../../../Common/Components/Modals/BaseModal';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import { deleteDeviceRequest } from '../../Request/DeleteDeviceRequest';
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import DeleteButton from '../../../Common/Components/Buttons/DeleteButton';
import { indexUrl } from '../../../Common/URLs/CommonURLs';
import { useMainIndicators } from '../../../Common/Components/Pages/MainPageTop';

export function DeleteDevice(props: {
    deviceName: string,
    deviceID: number,
}) {
    const { setRefreshNavbar } = useMainIndicators();
    
    const { deviceID, deviceName } = props;

    const [showDeleteDeviceModal, setShowDeleteDeviceModal] = useState<boolean>(false)

    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const navigate: NavigateFunction = useNavigate();

    const deleteDeviceHandler = async (e: Event) => {
        e.preventDefault();
        const deviceDeletedRequestPayload = await deleteDeviceRequest(deviceID);

        if (deviceDeletedRequestPayload.status === 200) {
            setShowDeleteDeviceModal(false);
            setRefreshNavbar(true);

            setAnnouncementModals([
                <AnnouncementFlashModalBuilder
                    title={`Success`}
                    dataToList={[`Device ${deviceName} deleted successfully`]}
                    setAnnouncementModals={setAnnouncementModals}
                    timer={2000}
                    />
            ]);

            setTimeout(() => {
                navigate(`${indexUrl}`);
            }, 2000);
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
            <DeleteButton clickFunction={setShowDeleteDeviceModal} />
            {
                showDeleteDeviceModal === true
                    ? 
                        <BaseModal
                            title={`Delete Device`}
                            modalShow={true}
                            setShowModal={setShowDeleteDeviceModal}
                        >
                            <>
                                <p style={{ textAlign: 'initial'}}>
                                    Are you sure you want to delete device: <b>{deviceName}</b>
                                    <br />
                                    with the ID of: <b>{deviceID}</b>
                                </p>
                                <SubmitButton
                                    type="submit"
                                    text='Delete Device'
                                    name='delete-device'
                                    action='submit'
                                    classes='add-new-submit-button'
                                    onClickFunction={deleteDeviceHandler}
                                />
                                <CloseButton 
                                    close={setShowDeleteDeviceModal} 
                                    classes={"modal-cancel-button"} 
                                />
                            </>
                        </BaseModal>
                    : 
                        null
            }
        </>
    );
}