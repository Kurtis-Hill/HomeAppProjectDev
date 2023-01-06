import * as React from 'react';
import { useState } from 'react';
import {
    Outlet,
} from "react-router-dom";

import Navbar from "../Navbar/Navbar";

import { AnnouncementFlashModal } from "../Modals/AnnouncementFlashModal";
import { BuildAnnouncementErrorFlashModal } from "../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";
import { ErrorResponseInterceptor } from "../../Request/Axios/ErrorResponseInterceptor";
import { RequestInterceptor } from "../../Request/Axios/RequestInterceptor";

import { SensorDataContextProvider } from "../SensorDataProvider/SensorDataProvider";

export function MainPageTop() {
    const [refreshNavbar, setRefreshNavbar] = useState<boolean>(true);

    const setRefreshNavDataFlag = (newValue: boolean) => {
        setRefreshNavbar(newValue);
    }

    const [errorAnnouncementErrorModals, setErrorAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const showErrorAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        setErrorAnnouncementErrorModals([
            ...errorAnnouncementErrorModals,
            <BuildAnnouncementErrorFlashModal
                title={title}
                errors={errors}
                errorNumber={errorAnnouncementErrorModals.length}
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
            <RequestInterceptor />
            <div id="page-top">
                <div id="wrapper">
                    <SensorDataContextProvider 
                    children={undefined}
                    >
                        <Navbar
                            refreshNavbar={refreshNavbar}
                            setRefreshNavDataFlag={setRefreshNavDataFlag}
                            showErrorAnnouncementFlash={showErrorAnnouncementFlash}
                            />
                        <Outlet
                            context={[setRefreshNavDataFlag, showErrorAnnouncementFlash]}
                            />
                    </SensorDataContextProvider>
                </div>
            </div>
        </React.Fragment>
    );
}
