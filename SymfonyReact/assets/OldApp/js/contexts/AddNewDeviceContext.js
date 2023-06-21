import React, { Component, createContext, useContext } from 'react';
import axios from 'axios';

import { apiURL } from '../Utilities/URLSCommon';
import { getAPIHeader } from '../Utilities/APICommon';

export const AddNewDeviceContext = createContext();

const emptyNewDeviceModalContent = {
    newDeviceName:'',
    newDevicePassword: '',
    newDeviceRoom:'',
    newDeviceGroup:'',
    newDeviceID:'',
    deviceSecret:null,
    errors:[],
    formSubmit:false
};

export default class AddNewDeviceContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            addNewDeviceModalToggle: false,
            newDeviceModalContent: emptyNewDeviceModalContent,
        }
    }

    handleNewDeviceFormSubmission = async (event) => {
        event.preventDefault();

        this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:true}});

        const formData = new FormData(event.target);
        
        const jsonFormData = {
            'deviceName' : this.state.newDeviceModalContent.newDeviceName,
            'devicePassword' : this.state.newDeviceModalContent.newDevicePassword,
            'deviceRoom' :  this.state.newDeviceModalContent.newDeviceRoom !== '' ? parseInt(this.state.newDeviceModalContent.newDeviceRoom) : parseInt(document.getElementById("deviceRoom").value),
            'deviceGroup' :  this.state.newDeviceModalContent.newDeviceGroup !== '' ? parseInt(this.state.newDeviceModalContent.newDeviceGroup) : parseInt(document.getElementById("deviceGroup").value),
        };

       try {
            const newDeviceSubmissionResponse = await axios.post(`${apiURL}user-devices/add-new-device`, jsonFormData, getAPIHeader());

            if (newDeviceSubmissionResponse.status === 201) {
                const responseData = newDeviceSubmissionResponse.data.payload;                
                this.setState({addNewDeviceModalSubmit: false});
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:false, deviceSecret: responseData.secret, errors:[], newDeviceID: responseData.deviceNameID}});
            } else {
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: ['unexpected response'], formSubmit:false}});
            }

        } catch (error) {
            if (error.response.data.errors && Array.isArray(error.response.data.errors) && error.response.data.errors.length > 0) {
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: error.response.data.errors, formSubmit:false}});
            } else {
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: ['Response not recognised'], formSubmit:false}});
            }
        }        
    }

    updateNewDeviceModalForm = (event) => {
        const formInput = event.target.value;
        switch (event.target.name) {
            case "device-room":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceRoom: formInput}});
                break;

            case "device-group":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceGroup: formInput}});
                break;

            case "device-name":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceName: formInput}});
                break;
            case "device-password":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDevicePassword: formInput}});
                break;
        }
    }

    toggleNewDeviceLoading = () => {
        this.setState({addNewDeviceModalLoading: !addNewDeviceModalLoading, newDeviceModalContent:emptyNewDeviceModalContent});
    }

    toggleNewDeviceModal = () => {
        console.log('clickedd!', this.state.addNewDeviceModalToggle);
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
