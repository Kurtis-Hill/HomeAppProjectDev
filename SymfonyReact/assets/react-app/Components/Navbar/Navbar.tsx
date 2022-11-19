import * as React from 'react';
import { useState, useEffect } from 'react';
import { Link, useNavigate } from "react-router-dom";
import axios, {AxiosError, AxiosResponse} from 'axios';

import { webappURL } from "../../Common/CommonURLs";

import { getRoles, checkAdmin } from "../../Session/UserSession";

import { handleNavBarRequest } from "../../Request/NavBar/NavBarRequest";
import NavBarResponseInterface from "../../Response/NavBar/NavBarResponseInterface";
import { ErrorResponseInterface } from "../../Response/ErrorResponseInterface";

import NavbarViewOptionListElements  from "./NavbarViewOptionListElements";

import HomeAppButton from "../Buttons/HomeAppButton"
import AdminButton from "../Buttons/AdminButton";

import { SiderbarDividerWithHeading } from "../Dividers/SiderbarDividerWithHeading";

import { AnnouncementFlashModal } from "../Modals/AnnouncementFlashModal";
import { BuildAnnouncementErrorFlashModal } from "../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";

export default function NavBar(props: {
    refreshNavbar: boolean,
    setRefreshNavDataFlag: (newValue: boolean) => void,
    showErrorAnnouncementFlash: (errors: Array<string>, title: string) => void
}) {
    const refreshNavbarIndicator = props.refreshNavbar;
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;
    const errorAnnouncementFlash = props.showErrorAnnouncementFlash;

    const [navbarAnnouncementErrorModals, setNavbarAnnouncementErrorModals] = useState<Array<typeof AnnouncementFlashModal>>([]);
    const [navbarResponseData, setNavbarResponseData] = useState<NavBarResponseInterface>([]);

    const admin: boolean = checkAdmin();

    useEffect(() => {
        console.log('use effect triggered', refreshNavbarIndicator);
        if (refreshNavbarIndicator === true) {
            requestNavbarData().then(r => {
                setRefreshNavDataFlag(false);
            });
        }
      }, [refreshNavbarIndicator]);

    const requestNavbarData = async (): Promise<AxiosResponse> => {
            console.log('nav request triggered')
        try {
            const navbarResponse: AxiosResponse = await handleNavBarRequest();
            const navbarResponseData: NavBarResponseInterface = navbarResponse.data.payload;

            setNavbarResponseData(navbarResponseData);
            if (navbarResponseData.errors.length > 0) {
                errorAnnouncementFlash(navbarResponseData.errors, 'Partial response');
            }

            return navbarResponse;
        } catch (err) {
            const errors = err as Error|AxiosError;
        
            if(axios.isAxiosError(errors)) {
                const jsonResponse: ErrorResponseInterface = JSON.parse(errors.request.response);
                let errorsOverride: boolean = false;
        
                if (errors.response.status === 403) {
                    errorsOverride = true
                }
        
                const errorsForModal = Array.isArray(jsonResponse.errors)
                    ? jsonResponse.errors
                    : [jsonResponse.errors];

                errorAnnouncementFlash(
                    errorsOverride === true
                        ? ['Unauthorized']
                        : errorsForModal,
                    jsonResponse.title
                );
            } else {
                errorAnnouncementFlash(
                    [`Something went wrong, please try refresh the browser or log out and back in again`],
                    'Unrecognized Error'
                );
            }
        }
    }

    return (
        <React.Fragment>
            {
                navbarAnnouncementErrorModals.map((navbarAnnouncementErrorModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            {navbarAnnouncementErrorModal}
                        </React.Fragment>
                    );
                })
            }

            <ul className={"navbar-nav bg-gradient-primary sidebar sidebar-dark accordion "}>
                <HomeAppButton />
                <hr className="sidebar-divider my-0" />
                {
                    admin === true
                        ? <li className="nav-item">
                            <AdminButton />
                        </li>
                        : null
                }
                <SiderbarDividerWithHeading heading="View options for:" />

                <NavbarViewOptionListElements navbarResponseData={navbarResponseData} />


                {/*<li className="nav-item">*/}
                {/*    <a className="nav-link" href="charts.html">*/}
                {/*        <i className="fas fa-fw fa-chart-area" />*/}
                {/*        <span>Charts</span></a>*/}
                {/*</li>*/}

                <hr className="sidebar-divider d-none d-md-block" />

                <div className="text-center d-none d-md-inline">
                    <button className="rounded-circle border-0" id="sidebarToggle" onClick={() => {}}/>
                </div>
            </ul>
        </React.Fragment>
    );
}
