import * as React from 'react';
import { useState, useEffect } from 'react';
import { AnnouncementFlashModal } from "../../Modals/AnnouncementFlashModal";
import { BuildAnnouncementErrorFlashModal } from "../../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";
import { ErrorResponseInterceptor } from "../../../Request/Axios/ErrorResponseInterceptor";
import { RequestInterceptor } from "../../../Request/Axios/RequestInterceptor";

// import { BuildAnnouncementErrorFlashModal } from "../../../Components/M"
export function ErrorResponseComponent(props) {
    const [errorAnnouncementErrorModals, setErrorAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const showErrorAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setErrorAnnouncementErrorModals(prevErrorArray => [
            ...errorAnnouncementErrorModals,
            <BuildAnnouncementErrorFlashModal
                title={title}
                dataToList={errors}
                dataNumber={errorAnnouncementErrorModals.length}
                timer={timer ? timer : 40}
            />
        ])
    }


    return (
        <React.Fragment>
            {
                errorAnnouncementErrorModals.map((errorAnnouncementErrorModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            {errorAnnouncementErrorModal}
                        </React.Fragment>
                    );
                })
            }
            <ErrorResponseInterceptor showErrorAnnouncementFlash={showErrorAnnouncementFlash} />
            {/* <RequestInterceptor /> */}
        </React.Fragment>
    );
}
