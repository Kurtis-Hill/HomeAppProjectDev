import * as React from 'react';
import { useState, useEffect } from 'react';
import { AnnouncementFlashModal, AnnouncementFlashModalInterface } from "../Modals/AnnouncementFlashModal";
import { AnnouncementFlashModalBuilder } from '../../Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { ResponseInterceptor } from '../../Response/Axios/ResponseInterceptor';

export function ResponseComponent(props: {refreshNavBar: (newValue: boolean) => void}) {
    const [announcementModals, setAnnouncementModals] = useState<React.JSX.Element[]>([]);

    const [announcementCount, setAnnouncementCount] = useState<number>(0);

    const refreshNavBar = props.refreshNavBar;

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
            <ResponseInterceptor showAnnouncementFlash={showAnnouncementFlash} refreshNavBar={refreshNavBar} />
        </>
    );
}
