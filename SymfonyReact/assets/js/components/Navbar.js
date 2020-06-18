import React, { Component, useContext, useState } from 'react';
import { Link } from 'react-router-dom';
import { NavbarContext } from '../contexts/NavbarContext';



const Navbar = () => {
    const context = useContext(NavbarContext);
    
    const navbarCollapse = context.navbarSize ? 'toggled' : '';
    
    const settingRoute = "HomeApp/settings/";
    const roomRoute = "HomeApp/rooms/";  
    const homeRoute = "HomeApp/index/";  
    
    return ( 
        <React.Fragment>         
               
      
            <ul className={"navbar-nav bg-gradient-primary sidebar sidebar-dark accordion "+ navbarCollapse} id="accordionSidebar">
      
                <Link className="sidebar-brand d-flex align-items-center justify-content-center" to={homeRoute}>
                    <div className="sidebar-brand-icon rotate-n-15">
                        <i className="fas fa-home" />
                    </div>
                    <div className="sidebar-brand-text mx-3">Home App <sup>2</sup></div>
                </Link>
              
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
               
              
                <li className="nav-item" onMouseEnter={() => {context.openNavElement('room')}} onMouseLeave={() => {context.closeNavElemnt('room')}}>
                    <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i className="fas fa-fw fa-person-booth"/>
                        <span>Room</span>
                    </a>
                    <div id="collapseTwo" className={context.navStyle('room')} aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">View Room:</h6>
                        {context.navRooms.map((navRoom, index) => (
                        
                            <a key={navRoom.r_roomid} className="collapse-item" href={roomRoute+navRoom.r_roomid}>{navRoom.r_room}</a>
                        ))}
                        {/* {context.navRooms} */}
                    </div>
                    </div>
                </li>
            
                <li className="nav-item" onMouseEnter={() => {context.openNavElement('settings')}} onMouseLeave={() => {context.closeNavElemnt('settings')}}>
                    <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                        <i className="fas fa-fw fa-wrench" />
                        <span>Settings</span>
                    </a>
                    <div id="collapseTwo" className={context.navStyle('settings')} aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">Settings:</h6>
                        {/* {context.navRooms} */}
                        {context.navRooms.map((navRoom, index) => (
                            // WANTS TO BE LINK
                            <a key={navRoom.r_roomid} className="collapse-item" href={settingRoute+navRoom.r_roomid}>{navRoom.r_room}</a>
                        ))}
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
    

            </React.Fragment>
        );
}

 
export default Navbar;