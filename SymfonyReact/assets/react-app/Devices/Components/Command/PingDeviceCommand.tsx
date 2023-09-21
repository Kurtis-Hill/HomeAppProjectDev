import * as React from 'react';
import { useState, useEffect } from 'react';
import { pingDeviceRequest } from '../../Request/PindDeviceRequest';
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';

export function PingDeviceCommand(props: { deviceID: number }) {
    const { deviceID } = props;
    
    const [pingDeviceLoading, setPingDeviceLoading] = useState<boolean>(false);
    
    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const pingDevice = async () => {
        setPingDeviceLoading(true);

        const response = await pingDeviceRequest(deviceID);

        if (response.status === 200) {
            setAnnouncementModals([
                <AnnouncementFlashModalBuilder
                    setAnnouncementModals={setAnnouncementModals}
                    title={response.data.title}
                    dataToList={['Device pinged successfully']}
                    timer={40}
                />
            ])
        }

        setPingDeviceLoading(false);
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
                pingDeviceLoading === false 
                    ? 
                        <SubmitButton
                            text="Ping Device"
                            name="ping-device"
                            // className="button is-primary"
                            onClickFunction={() => pingDevice()}
                        />
                    : 
                    <DotCircleSpinner classes="center-spinner bool-sensor-button-loading"  />
            }
                {/* Ping Device
            <div>
                { pingDeviceLoading ? <p>Loading...</p> : null }
                { pingDeviceResponse ? <p>{pingDeviceResponse}</p> : null }
            </div> */}
        </>
    );
}