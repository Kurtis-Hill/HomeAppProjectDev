import React, { Component, createContext } from 'react';
import ReactDOM from "react-dom";
import axios from 'axios';

import { getToken, webappURL, apiURL, getRefreshToken, setUserSession, lowercaseFirstLetter } from '../Utilities/Common';
import { array } from 'prop-types';

export const NavbarContext = createContext();

export default class NavbarContextProvider extends Component {
    constructor(props) {
        super(props);        
        this.state = {
            rooms: [],
            devices: [],
            sensorNames: [],
            groupNames: [],
            roomNavToggle: false,
            deviceSettingsNavToggle: false,
            showNavbarToggleSize: false,
            addNewDeviceModalToggle: false,
        }
    }
 
    componentDidMount() {
        this.navbarData();
    }

    //BEGGINING OF TAB METHODS
    toggleShowNavTabElement = (navDropDownElement) => {       
        if (navDropDownElement === 'room') {
            this.setState({roomNavToggle: !this.state.roomNavToggle});
        }
        
        if (navDropDownElement === 'device-settings') {
            this.setState({deviceSettingsNavToggle: !this.state.deviceSettingsNavToggle});
        }
    }

    toggleOffNavTabElement = (navDropDownElement) => {       
        if (navDropDownElement === 'room') {
            this.setState({roomNavToggle: false});
        }
        
        if (navDropDownElement === 'device-settings') {
            this.setState({deviceSettingsNavToggle: false});
        }
    }

    toggleOnNavTabElement = (navDropDownElement) => {       
        if (navDropDownElement === 'room') {
            this.setState({roomNavToggle: true});
        }
        
        if (navDropDownElement === 'device-settings') {
            this.setState({deviceSettingsNavToggle: true});
        }
    }

    navbarData = () => {
        axios.get(apiURL+'navbar/navbar-data',
        { headers: {"Authorization" : `Bearer ${getToken()}`} })
        .then(response => {
            const navBarRepsonse = response.data.responseData;
            this.setState({devices: navBarRepsonse.devices, rooms: navBarRepsonse.rooms, groupNames: navBarRepsonse.groupNames});
        }).catch(error => {     
            if (error.response.status === 500) {
                alert('Failed Getting Navbar Data, '+error.response.data.title);
            }
        })
    }

    navbarSizeToggle = () => {
        this.setState({showNavbarToggleSize: !this.state.showNavbarToggleSize});
    }

    toggleNewDeviceModal = () => {
        console.log('clicked');
        this.setState({addNewDeviceModalToggle: !this.state.addNewDeviceModalToggle});
    }
//  END OF TAB METHODS

    render() {
        return (
            <NavbarContext.Provider value={{
                toggleNavElement: this.toggleShowNavTabElement,
                userRooms: this.state.rooms,
                navbarSizeToggle: this.navbarSizeToggle,
                navbarSize: this.state.showNavbarToggleSize,
                userDevices: this.state.devices,
                groupNames: this.state.groupNames,
                roomNavToggle: this.state.roomNavToggle,
                deviceSettingsNavToggle: this.state.deviceSettingsNavToggle,
                toggleOffNavTabElement: this.toggleOffNavTabElement,
                toggleOnNavTabElement:this.toggleOnNavTabElement,
                toggleNewDeviceModal: this.toggleNewDeviceModal,
                addNewDeviceModalToggle: this.state.addNewDeviceModalToggle,
            }}>
                {this.props.children}
            </NavbarContext.Provider>
        )
    }
}
