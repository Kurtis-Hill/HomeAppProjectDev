import * as React from 'react';
import { AnnouncementFlashModal, AnnouncementFlashModalInterface } from "../../Components/Modals/AnnouncementFlashModal";
import { ResponseInterceptor } from '../Axios/ErrorResponseInterceptor';
import { AnnouncementFlashModalBuilder } from '../../Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { useState, useEffect } from 'react';

export function ResponseComponent() {
    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const [announcementCount, setAnnouncementCount] = useState<number>(0);

    useEffect(() => {
        console.log('response componenet fired');
    }, [announcementModals]);

    const showAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={errors}
                dataNumber={announcementCount}
                setErrorCount={setAnnouncementCount}
                timer={timer ? timer : 40}
            />
        ])
    }

    return (
        <>  
            {
                announcementModals.map((errorAnnouncementErrorModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            {errorAnnouncementErrorModal}
                        </React.Fragment>
                    );
                })
            }
            <ResponseInterceptor showAnnouncementFlash={showAnnouncementFlash} />
        </>
    );
}
