import * as React from 'react';
import { useState } from 'react';
import {
    Link,
    Outlet,
  } from "react-router-dom";

import Navbar from "../Navbar/Navbar";

import { AnnouncementFlashModal } from "../Modals/AnnouncementFlashModal";
import { BuildAnnouncementErrorFlashModal } from "../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";

export function MainPageTop() {
    const [refreshNavbar, setRefreshNavbar] = useState<boolean>(true);

    const setRefreshNavDataFlag = (newValue: boolean) => {
        setRefreshNavbar(newValue);
    }

    const [errorAnnouncementErrorModals, setErrorAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const showErrorAnnouncementFlash = (errors: Array<string>, title: string): void => {
        setErrorAnnouncementErrorModals([
            ...errorAnnouncementErrorModals,
            <BuildAnnouncementErrorFlashModal
                title={title}
                errors={errors}
                errorNumber={errorAnnouncementErrorModals.length}
                timer= {80}
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
