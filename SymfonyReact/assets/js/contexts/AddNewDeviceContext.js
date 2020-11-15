import React, { Component, createContext, useContext } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

import { getToken, apiURL } from '../Utilities/Common';

export const AddNewDeviceContext = createContext();

const emptyNewDeviceModalContent = {
    newDeviceName:'',
    newDeviceRoom:'',
    newDeviceGroup:'',
    newDeviceID:'',
    deviceSecret:null, 
    errors:[], 
    formSubmit:false};

export default class AddNewDeviceContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            addNewDeviceModalToggle: false,
            newDeviceModalContent: emptyNewDeviceModalContent,
        }
    }

    handleNewDeviceFormSubmission = (event) => {
        event.preventDefault();
        
        this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:true}});

        const formData = new FormData(event.target);
        
        console.log('formdata', formData, event.target);
        const config = {     
            headers: { 'Content-Type': 'multipart/form-data' , "Authorization" : `BEARER ${getToken()}` }
        }

        axios.post(apiURL+'devices/new-device/submit-form-data', formData, config)
        .then(response => {
            this.setState({addNewDeviceModalSubmit: false});
            this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:false, deviceSecret:response.data.responseData.secret, errors:[], newDeviceID: response.data.deviceID}});    
        })
        .catch(error => {
            const status = error.response.status;
            if (status === 400) {
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: [error.response.data.responseData], formSubmit:false, deviceSecret: null}});
            }
            if (status === 500) {
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: ['Server error'], formSubmit:false, deviceSecret: null}});
                alert('Something went wrong please try again');
            }
        })
    }

    updateNewDeviceModalForm = (event) => {
        const formInput = event.target.value;

        switch (event.target.name) {
            case "device-room":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceModalContent: formInput}});
                break;
            
            case "device-group":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceModalContent: formInput}});
                break;

            case "device-name":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceName: formInput}});
                break;
        }
    
    }

    toggleNewDeviceLoading = () => {
        this.setState({addNewDeviceModalLoading: !addNewDeviceModalLoading, newDeviceModalContent:emptyNewDeviceModalContent});
    }

    toggleNewDeviceModal = () => {
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
                toggleNewDeviceLoading: this.toggleNewDeviceLoading
            }}>
                {this.props.children}
            </AddNewDeviceContext.Provider>
        )
    }
}
