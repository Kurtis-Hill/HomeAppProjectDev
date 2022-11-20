import * as React from 'react';
import { useState, useEffect } from 'react';
import axios, {AxiosError, AxiosResponse} from 'axios';

import { checkAdmin } from "../../Session/UserSession";

import { handleNavBarRequest } from "../../Request/NavBar/NavBarRequest";
import NavBarResponseInterface from "../../Response/NavBar/NavBarResponseInterface";

import NavbarViewOptionListElements  from "./NavbarViewOptionListElements";

import HomeAppButton from "../Buttons/HomeAppButton"
import AdminButton from "../Buttons/AdminButton";

import { SidebarDividerWithHeading } from "../Dividers/SidebarDividerWithHeading";
import DotCircleSpinner from "../Spinners/DotCircleSpinner";


export default function NavBar(props: {
    refreshNavbar: boolean,
    setRefreshNavDataFlag: (newValue: boolean) => void,
    showErrorAnnouncementFlash: (errors: Array<string>, title: string, timer?: number|null) => void,
}) {
    const refreshNavbarIndicator = props.refreshNavbar;
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;
    const errorAnnouncementFlash = props.showErrorAnnouncementFlash;

    const [navbarResponseData, setNavbarResponseData] = useState<NavBarResponseInterface>([]);
    const [loadingNavbarListItems, setLoadingNavbarListItems] = useState<boolean>(true);

    const admin: boolean = checkAdmin();

    useEffect(() => {
        console.log('navbar use effect triggered', refreshNavbarIndicator);
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

            console.log('nav request code', navbarResponse.status);
            console.log('nav request', navbarResponseData);
            setNavbarResponseData(navbarResponseData);
            setLoadingNavbarListItems(false);

            return navbarResponse;
        } catch (err) {
            const errors = err as Error | AxiosError;
            console.log('nav request error', errors);
            if (!axios.isAxiosError(errors)) {
                errorAnnouncementFlash(
                    [`Something went wrong, please try refresh the browser or log out and back in again`],
                    'Unrecognized Error'
                );
            }
            setLoadingNavbarListItems(false);
        }
    }

    return (
        <React.Fragment>
            <ul className={"navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"}>
                <HomeAppButton />
                <hr className="sidebar-divider my-0" />
                {
                    admin === true
                        ? <li className="nav-item">
                            <AdminButton />
                        </li>
                        : null
                }
                <SidebarDividerWithHeading heading="View options for:" />

                {
                    loadingNavbarListItems === true
                        ? <DotCircleSpinner classes="margin-spinner" />
                        : null
                }
                <NavbarViewOptionListElements navbarResponseData={navbarResponseData} />
                {/*<NavbarViewOptionListElements navbarResponseData={navbarResponseData} />*/}

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
