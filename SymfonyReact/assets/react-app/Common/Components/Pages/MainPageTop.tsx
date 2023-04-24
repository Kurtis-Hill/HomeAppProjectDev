import * as React from 'react';
import { useState, useEffect } from 'react';
import {
    Outlet, useOutletContext
} from "react-router-dom";

import Navbar from "../../../UserInterface/Navbar/Components/Navbar";

import { AnnouncementFlashModal } from "../Modals/AnnouncementFlashModal";
import { AnnouncementFlashModalBuilder } from "../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";
import { RequestInterceptor } from "../../Request/Axios/RequestInterceptor";

import { SensorDataContextProvider } from "../../../Sensors/DataProviders/SensorDataProvider";

import { UserDataContextProvider } from '../../../User/DataProviders/UserDataContextProvider';
import { ErrorResponseComponent } from "../../Request/Interceptors/ErrorResponseComponent";

type ContextType = {
     showAnnouncementFlash: (errors: Array<string>, title: string, timer?: number | null) => void | null;
     setRefreshNavbar: (newValue: boolean) => void | null;
};

export function MainPageTop() {
    const [refreshNavbar, setRefreshNavbar] = useState<boolean>(true);
    
    const [announcementModals, setAnnouncementModals] = useState<Array<typeof AnnouncementFlashModal>>([]);

    const setRefreshNavDataFlag = (newValue: boolean) => {
        setRefreshNavbar(newValue);
    }

    const showAnnouncementFlash = (errors: Array<string>, title: string, timer?: number | null): void => {
        console.log('its coming!!', errors, title, timer);
        setAnnouncementModals([
            ...announcementModals,
            <AnnouncementFlashModalBuilder
                announcementModals={announcementModals}
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={errors}
                dataNumber={announcementModals.length}
                timer={timer ? timer : 40}
            />
        ])
    }

    return (
        <React.Fragment>
            <RequestInterceptor />
            <ErrorResponseComponent showErrorAnnouncementFlash={showAnnouncementFlash} announcementModals={announcementModals} />
            <div id="page-top">
                <div id="wrapper">
                    <UserDataContextProvider children={undefined}>
                        <SensorDataContextProvider children={undefined}>
                            <Navbar
                                refreshNavbar={refreshNavbar}
                                setRefreshNavDataFlag={setRefreshNavDataFlag}
                                showErrorAnnouncementFlash={showAnnouncementFlash}
                            />
                            <Outlet
                                context={
                                    {
                                        showAnnouncementFlash,
                                        setRefreshNavbar
                                    }
                                    // [
                                    //     setRefreshNavDataFlag,
                                    //     showAnnouncementFlash
                                    // ]
                                }
                            />
                        </SensorDataContextProvider>
                    </UserDataContextProvider>
                </div>
            </div>
        </React.Fragment>
    );
}

export function useMainIndicators() {
    return useOutletContext<ContextType>();
  }
