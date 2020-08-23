import React, { Component, useContext, useState } from 'react';
import { Link } from 'react-router-dom';
import { NavbarContext } from '../contexts/NavbarContext';



const Navbar = () => {
    const context = useContext(NavbarContext);
    
    const navbarCollapse = context.navbarSize ? 'toggled' : '';
    
    const settingRoute = "settings/";
    const roomRoute = "rooms/";  
    const homeRoute = "index/";  
    const newSensorRoute = "sensors/new-sensor";
    const newDeviceRoute = "devices/new-device";

    let deviceNavShowToggle = context.deviceSettingsNavToggle === true ? 'show' : '';
    let roomNavShowToggle = context.roomNavToggle === true ? 'show' : '';
    console.log('page', roomNavShowToggle);
    return ( 
        
            <ul className={"navbar-nav bg-gradient-primary sidebar sidebar-dark accordion "+ navbarCollapse} id="accordionSidebar">
      
                <a href={homeRoute} className="sidebar-brand d-flex align-items-center justify-content-center">
                    <div className="sidebar-brand-icon rotate-n-15">
                        <i className="fas fa-home" />
                    </div>
                    <div className="sidebar-brand-text mx-3">Home App <sup>2</sup></div>
                </a>
              
                <hr className="sidebar-divider my-0" />
              
                <li className="nav-item">
                    <a className="nav-link" href="index.html">
                        <i className="fas fa-fw fa-tachometer-alt" />
                        <span>Dashboard</span></a>
                </li>
              
                <hr className="sidebar-divider" />
              
                <div className="sidebar-heading">
                    Interface
                </div>
                             
                <li className="nav-item" onClick={() => {{context.toggleNavElement('room')}}} onMouseEnter={() => {context.toggleNavElement('room')}} onMouseLeave={() => {context.toggleOffNavTabElement('room')}}>
                    <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i className="fas fa-fw fa-person-booth"/>
                        <span>Room</span>
                    </a>
                    <div id="collapseTwo" className={'collapse '+ roomNavShowToggle} aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">View Room:</h6>
                        {context.navRooms.map((navRoom) => (                        
                            <a key={navRoom.r_roomid} className="collapse-item" href={roomRoute+navRoom.r_roomid}>{navRoom.r_room}</a>
                        ))}
                    </div>
                    </div>
                </li>
            
                <li className="nav-item" onClick={() => {{context.toggleNavElement('device-settings')}}} onMouseEnter={() => {context.toggleNavElement('device-settings')}} onMouseLeave={() => {context.toggleOffNavTabElement('device-settings')}}>
                    <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                        <i className="fas fa-fw fa-microchip" />
                        <span>Device Settings</span>
                    </a>
                    <div id="collapseTwo" className={'collapse '+deviceNavShowToggle} aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">Devices:</h6>
                        {context.userDevices.map((devices) => (
                            <a key={devices.devicename} className="collapse-item" href={settingRoute+devices.devicename}>{devices.devicename}</a>
                        ))}
                         <a href={newDeviceRoute} className="collapse-item">+Add New Device</a>
                    </div>
                    </div>                    
                </li>            

                <hr className="sidebar-divider" />
        
                <div className="sidebar-heading">
                    Addons
                </div>

                <li className="nav-item">
                    <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                        <i className="fas fa-fw fa-folder" />
                        <span>Pages</span>
                    </a>

                <div id="collapsePages" className="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">Login Screens:</h6>
                        <a className="collapse-item" href="login.html">Login</a>
                        <a className="collapse-item" href="register.html">Register</a>
                        <a className="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div className="collapse-divider" />
                        <h6 className="collapse-header">Other Pages:</h6>
                        <a className="collapse-item" href="404.html">404 Page</a>
                        <a className="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                    </div>
                </li>
          
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
                    <button className="rounded-circle border-0" id="sidebarToggle" onClick={() => {context.navbarSizeToggle()}}/>
                </div>
            </ul>
    
            
  
    );
}

 
export default Navbar;