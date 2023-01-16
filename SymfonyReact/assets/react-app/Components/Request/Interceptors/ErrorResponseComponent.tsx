import * as React from 'react';
import { useState, useEffect } from 'react';
import { AnnouncementFlashModal } from "../../Modals/AnnouncementFlashModal";
import { BuildAnnouncementErrorFlashModal } from "../../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";
import { ErrorResponseInterceptor } from "../../../Request/Axios/ErrorResponseInterceptor";

export function ErrorResponseComponent(props) {
    const [errorAnnouncementErrorModals, setErrorAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    console.log('hey its me', errorAnnouncementErrorModals);
    const showErrorAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setErrorAnnouncementErrorModals([
            ...errorAnnouncementErrorModals,
            <BuildAnnouncementErrorFlashModal
                announcementModals={errorAnnouncementErrorModals}
                setAnnouncementModals={setErrorAnnouncementErrorModals}
                title={title}
                dataToList={errors}
                dataNumber={errorAnnouncementErrorModals.length}
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
