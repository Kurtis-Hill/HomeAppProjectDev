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

export function MainPageTop() {
    const [refreshNavbar, setRefreshNavbar] = useState<boolean>(true);

    const setRefreshNavDataFlag = (newValue: boolean) => {
        setRefreshNavbar(newValue);
    }

    const [errorAnnouncementErrorModals, setErrorAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const showErrorAnnouncementFlash = (errors: Array<string>, title: string, timer? : number|null): void => {
        setErrorAnnouncementErrorModals([
            ...errorAnnouncementErrorModals,
            <BuildAnnouncementErrorFlashModal
                title={title}
                errors={errors}
                errorNumber={errorAnnouncementErrorModals.length}
                timer= {timer ? timer : 80}
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
                    <Navbar
                        refreshNavbar={refreshNavbar}
                        setRefreshNavDataFlag={setRefreshNavDataFlag}
                        showErrorAnnouncementFlash={showErrorAnnouncementFlash}
                    />
                    <Outlet
                        context={[setRefreshNavDataFlag, showErrorAnnouncementFlash]}
                    />
                </div>
            </div>
        </React.Fragment>
    );
}
