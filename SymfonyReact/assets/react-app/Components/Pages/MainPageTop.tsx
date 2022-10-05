import * as React from 'react';
import {
    Routes,
    Route,
    Link,
    Outlet,
  } from "react-router-dom";

import { webappURL } from "../../Common/CommonURLs";

export function MainPageTop() {
    return (
        <div id="page-top">
            <div id="wrapper">  
                <ul className={"navbar-nav bg-gradient-primary sidebar sidebar-dark accordion "} id="accordionSidebar">
                    <Link to={`${webappURL}index`} className="sidebar-brand d-flex align-items-center justify-content-center">
                        <div className="sidebar-brand-icon rotate-n-15">
                            <i className="fas fa-home" />
                        </div>
                        <div className="sidebar-brand-text mx-3">Home App <sup>2</sup></div>
                    </Link>
                    <hr className="sidebar-divider my-0" />

                <li className="nav-item">
                <Link to={`${webappURL}index`}> 
                    <div className="nav-link" href={webappURL+"index"}>
                        <i className="fas fa-fw fa-tachometer-alt" />
                    <span>Admin Dashboard</span></div>
                </Link>
                </li>

                <hr className="sidebar-divider" />

                <div className="sidebar-heading">
                    Interface
                </div>

                <li className="nav-item" onClick={() => {}}>
                    <a className="nav-link collapsed" href="SymfonyReact/assets/OldApp/js/components/Navbar#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i className="fas fa-fw fa-person-booth"/>
                        <span>Room</span>
                    </a>
                    <div id="collapseTwo" className='collapse' aria-labelledby="headingTwo" data-parent="#accordionSidebar">
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
                        <span>Device Settings</span>
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
                <Outlet />
            </div>
        </div>
    );
}