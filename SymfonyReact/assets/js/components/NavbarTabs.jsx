import React, { Component, useContext, useState } from 'react';
import { Link } from 'react-router-dom'
import Navbar from './Navbar'


const DropDown = ({ newRoomState }) => {
    const roomState = newRoomState ? 'collapse show' : 'collapse';
    return roomState;
}
const NavbarTabs = () => {

    // const [openNavElemnt, setopenNavElemnt] = useState(false);

    // const [ navRoomStyleToggle, setNavRoomStyleToggle ] = useState(false);
    // // const [ navSettingStyleToggle, setnavSettingStyleToggle ] = useState(false);


    // const openNavElement = navElement => {        
    //     if (navElement === 'room') {
    //         const newRoomState = !navRoomStyleToggle;
    //         console.log('this is the hook', newRoomState);    

    //         return (
    //             <DropDown roomState={newRoomState}>
    //                 <div id="collapseTwo" className="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
    //                 <div className="bg-white py-2 collapse-inner rounded">
    //                     <h6 className="collapse-header">Custom Components:</h6>
    //                     <a className="collapse-item" href="buttons.html">Buttons</a>
    //                     <a className="collapse-item active" href="cards.html">Cards</a>
    //                 </div>
    //                 </div>
    //             </DropDown>
    //         )
    //     }

    //     if (navElement === 'settings') {
    //         const newSettingState = !this.state.settingsNavshow;
    //         this.setState({settingsNavshow: newSettingState})
    //         console.log(newSettingState);    
    //     }
    // }

    // const settingRoute = "HomeApp/settings/";
    // const roomRoute = "HomeApp/rooms/";  
    // const homeRoute = "HomeApp/index/";  


    return (
        <React.Fragment>
            <hr className="sidebar-divider" />
            <div className="sidebar-heading">
                Interface
            </div>
            {/* Nav Item - Pages Collapse Menu */}
            {/* <!--TODO OnMouseEnter currently messing up mobile when taken out mobile returns to single click to open tab --!>*/}
            <li className="nav-item" onMouseEnter={() => {context.openNavElement('room')}} onMouseLeave={() => {context.mouseLeaveNav('room')}} onMouseDown={() => {context.openNavElement('room')}}>
                <a className="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i className="fas fa-fw fa-cog" />
                        <span>Room</span>
                </a>
                <div id="collapseTwo" className={context.navStyle('room')} aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div className="bg-white py-2 collapse-inner rounded">
                        <h6 className="collapse-header">Custom Components:</h6>
                        <a className="collapse-item" href="buttons.html">Buttons</a>
                        <a className="collapse-item active" href="cards.html">Cards</a>
                    </div>
                </div>
            </li>
            <hr className="sidebar-divider" />
             
        </React.Fragment>
    )
}