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

    componentDidMount() {
        const newDeviceGroup = document.getElementById("deviceGroup");
        const newDeviceRoom = document.getElementById("deviceRoom");

        this.setState({
            emptyNewDeviceModalContent: {...this.state.emptyNewDeviceModalContent,
                newDeviceGroup: newDeviceGroup,
                newDeviceRoom: newDeviceRoom,
            }
        });
    }

    handleNewDeviceFormSubmission = (event) => {
        event.preventDefault();

        this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:true}});

        const formData = new FormData(event.target);

        const config = {
            headers: { 'Content-Type': 'multipart/form-data' , "Authorization" : `BEARER ${getToken()}` }
        }

        axios.post(apiURL+'devices/new-device/submit-form-data', formData, config)
            .then(response => {
                const responseData = response.data.responseData;
                this.setState({addNewDeviceModalSubmit: false});
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, formSubmit:false, deviceSecret: responseData.secret, errors:[], newDeviceID: responseData.deviceID}});
            })
            .catch(error => {
                const status = error.response.status;
               // const responseData = error.data.responseData;

                console.log(error.response.data.responseData);
                if (status === 400) {
                    this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: error.response.data.responseData, formSubmit:false}});
                }
                if (status === 500) {
                    this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, errors: ['Server error'], formSubmit:false}});
                }
            })
    }

    updateNewDeviceModalForm = (event) => {
        const formInput = event.target.value;
        switch (event.target.name) {
            case "deviceRoom":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceRoom: formInput}});
                break;

            case "deviceGroup":
                this.setState({newDeviceModalContent:{...this.state.newDeviceModalContent, newDeviceGroup: formInput}});
                break;

            case "deviceName":
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
