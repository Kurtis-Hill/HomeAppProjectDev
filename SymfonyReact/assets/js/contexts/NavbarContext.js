import React, { Component, createContext } from 'react';
import ReactDOM from "react-dom";
import axios from 'axios';

import { getToken } from '../Utilities/Common';

export const NavbarContext = createContext();

const emptynewDeviceModalContent = {newDeviceName:'', deviceGroupNames:[], deviceRoom:[], deviceSecret:null, errors:[], formSubmit:false}

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
            newDeviceModalContent: emptynewDeviceModalContent,
        }
    }

    
    componentDidMount() {
        this.navbarData();
        // this.navbarDeviceLinks();
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

    navbarData = () => {
        axios.get('/HomeApp/navbar/navbar-data',
        { headers: {"Authorization" : `Bearer ${getToken()}`} })
        .then(response => {
            this.setState({devices: response.data.devices, rooms: response.data.rooms, groupNames: response.data.groupNames});
        }).catch(error => {
            console.log(error);
        })
    }

    navbarSizeToggle = () => {
        this.setState({showNavbarToggleSize: !this.state.showNavbarToggleSize});
    }
//  END OF TAB METHODS

// START OF ADD NEW DEVICE METHODS
// Can be refactored out after finsihed make a Navbar class use component drilling to share state and break into smaller componenets
    toggleNewDeviceModal = () => {
        this.setState({addNewDeviceModalToggle: !this.state.addNewDeviceModalToggle});
    }

    toggleNewDeviceLoading = () => {
        this.setState({addNewDeviceModalLoading: !addNewDeviceModalLoading, newDeviceModalContent:emptynewDeviceModalContent});
    }

    handleNewDeviceFormSubmission = (event) => {
        event.preventDefault();

        this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:true}});

        const formData = new FormData(event.target);

        const config = {     
            headers: { 'Content-Type': 'multipart/form-data' , "Authorization" : `BEARER ${getToken()}` }
        }

        axios.post('/HomeApp/devices/new-device/modal-data', formData, config)
        .then(response => {
            console.log('submit response', response.data);
            this.setState({addNewDeviceModalSubmit: false});
            this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:false, deviceSecret:response.data, errors:[]}});    
            console.log('secret', this.state.newDeviceModalContent.deviceSecret);
        })
        .catch(error => {
            const status = error.response.status;
            if (status === 400) {
                console.log('FAILEDDD');
                console.log('errors', error.response.data);
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: error.response.data.errors, formSubmit:false, deviceSecret: null}});
                console.log(this.state.newDeviceModalContent.errors);
            }
            if (status === 500) {
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: ['Server error'], formSubmit:false, deviceSecret: null}});
                alert('Something went wrong please try again');
            }
        })
    }

    updateNewDeviceModalForm = (event) => {
        const formInput = event.target.value;
        switch(event.target.name) {
            case "device-name":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceName: formInput}});
                break;

            case "group-name":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, device: formInput}});
                break;

            case "room-name":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, deviceRoom: formInput}});
                break;
        }
    }




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
                toggleNewDeviceModal: this.toggleNewDeviceModal,
                addNewDeviceModalToggle: this.state.addNewDeviceModalToggle,
                newDeviceModalContent: this.state.newDeviceModalContent,
                updateNewDeviceModalForm: this.updateNewDeviceModalForm,
                handleNewDeviceFormSubmission: this.handleNewDeviceFormSubmission,

            }}>
                {this.props.children}
            </NavbarContext.Provider>
        )
    }
}
