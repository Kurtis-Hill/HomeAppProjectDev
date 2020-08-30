import React, { Component, createContext } from 'react';
import ReactDOM from "react-dom";
import axios from 'axios';

import { getToken } from '../Utilities/Common';

export const NavbarContext = createContext();

const emptynewDeviceModalContent = {newDeviceName:'', deviceGroupNames:[], errors:[], formSubmit:null}

export default class NavbarContextProvider extends Component {
    constructor(props) {
        super(props);        
        this.state = {
            rooms: [],
            devices: [],
            sensorNames: [],
            roomNavToggle: false,
            deviceSettingsNavToggle: false,
            showNavbarToggleSize: false,
            addNewDeviceModalToggle: false,
            addNewDeviceModalLoading: false,
            newDeviceModalContent: emptynewDeviceModalContent,
        }
    }

    
    componentDidMount() {
        this.navbarRoomLinks();
        this.navbarDeviceLinks();
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

    navbarRoomLinks = () => {
        axios.get('/HomeApp/Navbar/rooms',
        { headers: {"Authorization" : `BEARER ${getToken()}`} })
        .then(response => {
            this.setState({rooms: response.data})
        }).catch(error => {
            console.log(error);
        })
    }

    navbarDeviceLinks = () => {
        axios.get('/HomeApp/Navbar/devices',
        { headers: {"Authorization" : `Bearer ${getToken()}`} })
        .then(response => {
            this.setState({devices: response.data});
        }).catch(error => {
            console.log(error);
        })
    }

    navbarSizeToggle = () => {
        this.setState({showNavbarToggleSize: !this.state.showNavbarToggleSize});
    }
//  END OF TAB METHODS

// START OF ADD NEW DEVICE METHODS
// Can be refactored out after finsihed 
    toggleNewDeviceModal = () => {
        this.setState({addNewDeviceModalToggle: !this.state.addNewDeviceModalToggle});
    }

    toggleNewDeviceLoading = () => {
        this.setState({addNewDeviceModalLoading: !addNewDeviceModalLoading});
    }

    getNewDeviceModalContent = () => {

    }

    handleNewDeviceFormSubmission = () => {

    }

    updateNewDeviceModalForm = (event) => {
        const formInput = event.targe.value;

        switch(event.target.name) {
            case "device-name":
                this.setState({newDeviceModalContent:{...newDeviceModalContent, newDeviceName: formInput}});
                break;

            case "device-name":
                this.setState({newDeviceModalContent:{...newDeviceModalContent, device: formInput}});
                break;
        }

        console.log('key up device modal', formInput);
    }




    render() {
        return (
            <NavbarContext.Provider value={{
                toggleNavElement: this.toggleShowNavTabElement,
                userRooms: this.state.rooms,
                navbarSizeToggle: this.navbarSizeToggle,
                navbarSize: this.state.showNavbarToggleSize,
                userDevices: this.state.devices,
                roomNavToggle: this.state.roomNavToggle,
                deviceSettingsNavToggle: this.state.deviceSettingsNavToggle,
                toggleOffNavTabElement: this.toggleOffNavTabElement,
                toggleNewDeviceModal: this.toggleNewDeviceModal,
                addNewDeviceModalToggle: this.state.addNewDeviceModalToggle,
                addNewDeviceModalLoading: this.state.addNewDeviceModalLoading,
                newDeviceModalContent: this.state.newDeviceModalContent,

            }}>
                {this.props.children}
            </NavbarContext.Provider>
        )
    }
}
