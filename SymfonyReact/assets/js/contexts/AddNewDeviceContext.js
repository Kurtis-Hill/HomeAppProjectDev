import React, { Component, createContext, useContext } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

import { getToken } from '../Utilities/Common';

export const AddNewDeviceContext = createContext();

const emptynewDeviceModalContent = {newDeviceName:'', newDeviceRoom:'', newDeviceGroup:'', newDeviceID:'',  deviceSecret:null, errors:[], formSubmit:false};

export default class AddNewDeviceContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            addNewDeviceModalToggle: false,
            newDeviceModalContent: emptynewDeviceModalContent,
        }
    }

    handleNewDeviceFormSubmission = (event) => {
        event.preventDefault();
        
        this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:true}});
        
        const roomNameElement = document.getElementById('device-room');
        const deviceRoom = roomNameElement.options[roomNameElement.selectedIndex].value;

        const groupNameElement = document.getElementById('group-name');
        const deviceGroup = groupNameElement.options[groupNameElement.selectedIndex].value;

        const formData = new FormData(event.target);
        
        const config = {     
            headers: { 'Content-Type': 'multipart/form-data' , "Authorization" : `BEARER ${getToken()}` }
        }

        axios.post('/HomeApp/devices/new-device/modal-data', formData, config)
        .then(response => {
            console.log('submit response', response.data);
            this.setState({addNewDeviceModalSubmit: false});
            this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:false, deviceSecret:response.data.secret, errors:[], newDeviceRoom:deviceRoom, newDeviceGroup:deviceGroup, newDeviceID: response.data.deviceID}});    
            console.log('secret', this.state.newDeviceModalContent.deviceSecret);
        })
        .catch(error => {
            const status = error.response.status;
            if (status === 400) {
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
        this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceName: formInput}});
    }

    toggleNewDeviceLoading = () => {
        this.setState({addNewDeviceModalLoading: !addNewDeviceModalLoading, newDeviceModalContent:emptynewDeviceModalContent});
    }

    toggleNewDeviceModal = () => {
        console.log('clicked');
        this.setState({addNewDeviceModalToggle: !this.state.addNewDeviceModalToggle});
    }

    render() {
        return (
            <AddNewDeviceContext.Provider value={{
                toggleNewDeviceModal: this.toggleNewDeviceModal,
                addNewDeviceModalToggle: this.state.addNewDeviceModalToggle,
                newDeviceModalContent: this.state.newDeviceModalContent,
                updateNewDeviceModalForm: this.updateNewDeviceModalForm,
                handleNewDeviceFormSubmission: this.handleNewDeviceFormSubmission,
                toggleNewDeviceModal: this.toggleNewDeviceModal,
                toggleNewDeviceLoading: this.toggleNewDeviceLoading
            }}>
                {this.props.children}
            </AddNewDeviceContext.Provider>
        )
    }
}
