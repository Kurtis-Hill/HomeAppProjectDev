import { useState, useEffect } from 'react';
import * as React from 'react';
import { Link, useNavigate } from "react-router-dom";
import axios, {AxiosError} from 'axios';

import { webappURL } from "../../Common/CommonURLs";

import { getRoles, checkAdmin } from "../../session/UserSession";

import { handleNavBarRequest } from "../../Request/NavBar/NavBarRequest";
import NavBarResponseInterface from "../../Response/NavBar/NavBarResponseInterface";

import { AnnouncementFlashModal } from "../Modals/AnnouncementFlashModal";
import { BuildAnnouncementFlashModal } from "../../Builders/ModalBuilder/AnnouncementFlashModalBuilder";

export default function NavBar() {
    const [roomNavToggle, setRoomNavToggle] = useState<boolean>(false);

    const [userRooms, setUserRooms] = useState([]);
    const [userDevices, setUserDevices] = useState([]);
    const [userGroups, setUserGroups] = useState([]);
    const [navbarAnnouncementErrorModals, setNavbarAnnouncementErrorModals] = useState([]);

    const admin: boolean = checkAdmin();

    useEffect(() => {
        navbarRequestData();
      }, []);


    const toggleNavTabElement = (navDropDownElement: string): void => {      
        if (navDropDownElement === 'room') {
            setRoomNavToggle(!roomNavToggle);
        }
    }

    const toggleOffNavTabElement = (navDropDownElement): void => {       
        if (navDropDownElement === 'room') {
            setRoomNavToggle(false);
        }
    }

    const showErrorAnnouncementFlash = (errors: Array<string>, title: string): void => {        
        setNavbarAnnouncementErrorModals([
            <BuildAnnouncementFlashModal
                title={title}
                errors={errors}
            />
        ])
    }
    
    const navbarRequestData = async () => {
        try {
            const navbarResponse: NavBarResponseInterface = await handleNavBarRequest();
            setUserRooms(navbarResponse.userRooms);
            setUserDevices(navbarResponse.devices);
            setUserGroups(navbarResponse.groupNames);

            if (navbarResponse.errors.length > 0) {
                showErrorAnnouncementFlash(navbarResponse.errors, 'Partial response');
            }
        } catch (err) {
            const errors = err as Error|AxiosError;

            if(axios.isAxiosError(errors)) {
                const jsonResponse: {title: string, errors: string|Array<string>} = JSON.parse(errors.request.response);
                
                let errorsOverride: boolean = false;
                if (errors.response.status === 403) {
                    errorsOverride = true
                }
                console.log('json', jsonResponse)

                const errorsForModal = Array.isArray(jsonResponse.errors) 
                    ? jsonResponse.errors 
                    : [jsonResponse.errors];

                showErrorAnnouncementFlash(
                    errorsOverride === true 
                        ? ['Unauthorized'] 
                        : errorsForModal,
                    jsonResponse.title
                ); 
            } else {
                showErrorAnnouncementFlash(
                    [`Something went wrong, please try refresh the browser or log out and back in again`],
                    'Unrecognized Error'
                );
            }
          }
    }

    const roomNavToggleClass: string = roomNavToggle === true ? 'show' : '';

    return (
        <React.Fragment>
            {
                navbarAnnouncementErrorModals.map((navbarAnnouncementErrorModal, index) => {
                    return (
                        <div key={index}> 
                            {navbarAnnouncementErrorModal}
                        </div>
                        );
                })
            }
            <ul className={"navbar-nav bg-gradient-primary sidebar sidebar-dark accordion "}>
                <Link to={`${webappURL}index`} className="sidebar-brand d-flex align-items-center justify-content-center">
                    <div className="sidebar-brand-icon rotate-n-15">
                        <i className="fas fa-home" />
                    </div>
                    <div className="sidebar-brand-text mx-3">Home App <sup>2</sup></div>
                </Link>
                <hr className="sidebar-divider my-0" />

                <li className="nav-item">
                    {admin === true 
                        ? 
                        <Link to={`${webappURL}admin`}> 
                            <div className="nav-link" href={webappURL+"index"}>
                                <i className="fas fa-fw fa-tachometer-alt" />
                            <span>Admin Dashboard</span></div>
                        </Link>
                        : 
                        null
                    }
                </li>
                <hr className="sidebar-divider" />

                <div className="sidebar-heading">
                    View options
                </div>

                <li className="nav-item" onClick={() => {toggleNavTabElement('room')}}>
                    <div className="nav-link collapsed hover" data-toggle="collapse" aria-expanded="true" aria-controls="collapseUtilities">
                        <i className="fas fa-fw fa-person-booth"/>
                        <span>Room</span>
                    </div>
                    <div className={'collapse '+roomNavToggleClass} aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div className="bg-white py-2 collapse-inner rounded">
                            <h6 className="collapse-header">View Room:</h6>
                                {/* {context.userRooms.map((navRoom) => (
                                    <Link to={`${webappURL}room?room-id=${navRoom.roomID}`} key={navRoom.roomID} className="collapse-item">{navRoom.roomName}</Link>
                                ))}
                                <Link to={`${webappURL}add-room`} className="collapse-item">+Add New Room</Link> */}
                        </div>
                    </div>
                </li>


                        <li className="nav-item" onClick={() => {}}>
                            <a className="nav-link collapsed" href="SymfonyReact/assets/OldApp/js/components/Navbar#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                                <i className="fas fa-fw fa-microchip" />
                                <span>Devices</span>
                            </a>
                            <div id="collapseTwo" className='collapse' aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                            <div className="bg-white py-2 collapse-inner rounded">
                                <h6 className="collapse-header">Devices:</h6>
                                {/* {context.userDevices.map((device) => (
                                    <Link key={device.deviceNameID} className="collapse-item" to={`${webappURL}device?device-id=${device.deviceNameID}&device-group=${device.groupNameID}&device-room=${device.roomID}&view=device`}>{device.deviceName}</Link>
                                ))} */}
                                <div className="hover collapse-item" onClick={() => {}}>+Add New Device</div>
                            </div>
                            </div>
                        </li>

                        <hr className="sidebar-divider" />

                        <div className="sidebar-heading">
                            Addons
                        </div>

                        {/*<li className="nav-item">*/}
                        {/*    <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">*/}
                        {/*        <i className="fas fa-fw fa-folder" />*/}
                        {/*        <span>Pages</span>*/}
                        {/*    </a>*/}

                        {/*    <div id="collapsePages" className="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">*/}
                        {/*        <div className="bg-white py-2 collapse-inner rounded">*/}
                        {/*            <h6 className="collapse-header">Login Screens:</h6>*/}
                        {/*            <a className="collapse-item" href="login.html">Login</a>*/}
                        {/*            <a className="collapse-item" href="register.html">Register</a>*/}
                        {/*            <a className="collapse-item" href="forgot-password.html">Forgot Password</a>*/}
                        {/*            <div className="collapse-divider" />*/}
                        {/*            <h6 className="collapse-header">Other Pages:</h6>*/}
                        {/*            <a className="collapse-item" href="404.html">404 ColouredPage</a>*/}
                        {/*            <a className="collapse-item" href="blank.html">Blank ColouredPage</a>*/}
                        {/*        </div>*/}
                        {/*    </div>*/}
                        {/*</li>*/}

                        <li className="nav-item">
                            <a className="nav-link" href="charts.html">
                                <i className="fas fa-fw fa-chart-area" />
                                <span>Charts</span></a>
                        </li>

                        <li className="nav-item">
                            <a className="nav-link" href="tables.html">
                                <i className="fas fa-fw fa-table" />
                                <span>Tables</span></a>
                        </li>

                        <hr className="sidebar-divider d-none d-md-block" />

                        <div className="text-center d-none d-md-inline">
                            <button className="rounded-circle border-0" id="sidebarToggle" onClick={() => {}}/>
                        </div>
            </ul>
        </React.Fragment>
    );
}