import * as React from 'react';
import { useState } from 'react';
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import {pingDeviceRequest} from "../../Request/PingDeviceRequest";

export function RestartDeviceButton(props: { deviceID: number }) {
    const { deviceID } = props;

    const [restartDeviceLoading, setRestartDeviceLoading] = useState<boolean>(false);

    const [announcementModals, setAnnouncementModals] = useState<React.JSX.Element[]>([]);

    const restartDevice = async () => {
        setRestartDeviceLoading(true);

        const response = await pingDeviceRequest(deviceID);
        if (response?.status &&  response.status === 200) {
            setAnnouncementModals([
                <AnnouncementFlashModalBuilder
                    setAnnouncementModals={setAnnouncementModals}
                    title={response.data.title}
                    dataToList={['Device restarted successfully']}
                    timer={40}
                />
            ])
        }

        setRestartDeviceLoading(false);
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
            {
                restartDeviceLoading === false 
                    ? 
                        <div>
                            <SubmitButton
                                text="Restart Device"
                                name="restart-device"
                                onClickFunction={() => restartDevice()}
                            />
                        </div>
                    : 
                    <DotCircleSpinner classes="center-spinner bool-sensor-button-loading"  />
            }
        </>
    );
}
