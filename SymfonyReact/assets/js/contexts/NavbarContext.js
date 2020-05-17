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
            sensorNames: [],
            roomNavToggle: false,
            settingsNavToggle: false,
            navbarToggle: false,
        }
      //  this.navbarRoomLinks();
    }

    
    componentDidMount() {
        //if HomeApp/index fetchIndexCardData if Rooms fetch cardsForRoom()
        // this.axiosToken();
      
        this.navbarRoomLinks();
    }

    //BEGGINING OF TAB METHODS
    openNavTabElement = (navDropDownElement) => {       
        if (navDropDownElement === 'room') {
            this.setState({roomNavToggle: !this.state.roomNavToggle}) 
        }
        
        if (navDropDownElement === 'settings') {
            this.setState({settingsNavToggle: !this.state.settingsNavToggle}) 
        }
    }

    closeNavTabElement = (navDropDownElement) => {
        if (navDropDownElement === 'room') {
            this.setState({roomNavToggle: false}) 
        }
        
        if (navDropDownElement === 'settings') {
            this.setState({settingsNavToggle: false}) 
        }
    }


    navTabToggleStyle = (tab) => {
        if (tab === 'room') {
            const navRoomStyle = this.state.roomNavToggle === true ? 'collapse show' : 'collapse';
            return navRoomStyle;
        }

        if (tab === 'settings') {
        const navSettingsStyle = this.state.settingsNavToggle === true ? 'collapse show' : 'collapse';
        return navSettingsStyle;
        }
    }


    navbarRoomLinks = () => {
        axios.get('/HomeApp/Navbar/rooms',
        { headers: {"Authorization" : `Bearer ${token}`} })
        .then(response => {
            console.log('NavbarRoomLinks', response.data);
            this.setState({rooms: response.data})
        }).catch(error => {
            console.log(error);
        })
    }

    navbarSizeToggle = () => {
        // let currentNavState = !this.state.navBarToggle;
        console.log('nav toggle pressed')
        this.setState({navbarToggle: !this.state.navbarToggle});
        console.log(this.state.navbarToggle);
    }
//  END OF TAB METHODS


    render() {
        return (
            <NavbarContext.Provider value={{
                openNavElement: this.openNavTabElement,
                navStyle: this.navTabToggleStyle,
                closeNavElemnt: this.closeNavTabElement,
                navRooms: this.state.rooms,
                navbarSizeToggle: this.navbarSizeToggle,
                navbarSize: this.state.navbarToggle,
            }}>
                {this.props.children}
            </NavbarContext.Provider>
        )
    }
}
