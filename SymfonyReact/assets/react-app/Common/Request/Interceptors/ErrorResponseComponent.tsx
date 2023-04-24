import * as React from 'react';
import { useState, useEffect } from 'react';
import { AnnouncementFlashModalBuilder } from '../../Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import { AnnouncementFlashModal } from "../../Components/Modals/AnnouncementFlashModal";
import { ErrorResponseInterceptor } from '../Axios/ErrorResponseInterceptor';

export function ErrorResponseComponent(props) {
    const [errorAnnouncementErrorModals, setErrorAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);
    const [errorCount, setErrorCount] = useState<number>(0);

    const showErrorAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setErrorAnnouncementErrorModals([
            ...errorAnnouncementErrorModals,
            <AnnouncementFlashModalBuilder
                announcementModals={errorAnnouncementErrorModals}
                setAnnouncementModals={setErrorAnnouncementErrorModals}
                title={title}
                dataToList={errors}
                dataNumber={errorCount}
                timer={timer ? timer : 40}
            />
        ])
    }

    return (
        <React.Fragment>
            <ErrorResponseInterceptor showErrorAnnouncementFlash={showErrorAnnouncementFlash} />
            {
                errorAnnouncementErrorModals.map((errorAnnouncementErrorModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            {errorAnnouncementErrorModal}
                        </React.Fragment>
                    );
                })
            }
        </React.Fragment>
    );
}
