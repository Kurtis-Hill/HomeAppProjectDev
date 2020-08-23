import React, { Component, createContext } from 'react';
import ReactDOM from "react-dom";
import axios from 'axios';

export const NavbarContext = createContext();

const token = sessionStorage.getItem('token');

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
        { headers: {"Authorization" : `Bearer ${token}`} })
        .then(response => {
            this.setState({rooms: response.data})
        }).catch(error => {
            console.log(error);
        })
    }

    navbarDeviceLinks = () => {
        axios.get('/HomeApp/Navbar/devices',
        { headers: {"Authorization" : `Bearer ${token}`} })
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


    render() {
        return (
            <NavbarContext.Provider value={{
                toggleNavElement: this.toggleShowNavTabElement,
                navRooms: this.state.rooms,
                navbarSizeToggle: this.navbarSizeToggle,
                navbarSize: this.state.showNavbarToggleSize,
                userDevices: this.state.devices,
                roomNavToggle: this.state.roomNavToggle,
                deviceSettingsNavToggle: this.state.deviceSettingsNavToggle,
                toggleOffNavTabElement: this.toggleOffNavTabElement,

            }}>
                {this.props.children}
            </NavbarContext.Provider>
        )
    }
}
