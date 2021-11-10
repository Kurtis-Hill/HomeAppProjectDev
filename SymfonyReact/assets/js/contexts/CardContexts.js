import React, {Component, createContext} from 'react'
import axios from 'axios';

import {lowercaseFirstLetter, setUserSession} from '../Utilities/Common';
import {apiURL, webappURL} from '../Utilities/URLSCommon';
import {getAPIHeader, getRefreshToken} from '../Utilities/APICommon';

export const CardContext = createContext();

const emptyModalContent = {
    cardViewID: null,
    sensorData: [],
    cardIcon: [],
    cardColour: '',
    currentViewState: [],
};

const emptyUserSelectionData = {
    userIconSelections: [],
    userColourSelections: [],
    userCardViewSelections: [],
}

const emptyModalStatus = {
    submitSuccess: false,
    errors: [],
    modalSubmit: false,
}

class CardContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            refreshTimer: 3000,
            cardData: null,
            modalShow: false,
            modalLoading: false,
            modalContent: emptyModalContent,
            userSelectionData: emptyUserSelectionData,
            modalStatus: emptyModalStatus,
            url: '',
            errors: [],
            alternativeDisplayMessage: 'Loading...',
        };
    }

    componentDidMount() {
        this.setURL();
        this.cardRefreshTimerID = setInterval(
            () => this.fetchCardData(),
            this.state.refreshTimer
        );
    }


    componentDidUpdate(prevProps, preState) {
        // this.setURL();
        //TODO compare states display up/down arrow for reading level
        // console.log('prev state', preState);
        // console.log('prev props', prevProps);
    }

    componentWillUnmount() {
        clearInterval(this.cardRefreshTimerID);
    }


    setURL = () => {
        const cardAPI = apiURL+'card-data/cards';
        if (window.location.pathname === webappURL+'index') {
            this.setState({url: cardAPI});
            return;
        }

        const windowLocation = window.location.search;
        const urlParam = new URLSearchParams(windowLocation);

        const deviceName = urlParam.get('device-id');
        const deviceGroup = urlParam.get('device-group');
        const deviceRoom = urlParam.get('device-room');

        const currentGetParameters = `${cardAPI}?device-id=${deviceName}&device-group=${deviceGroup}+&device-room=${deviceRoom}+&view=device`;

// DEV needs sorting not proper
        if (currentGetParameters === `${this.state.url}`) {
            return;
        } else {
            this.setState({url: currentGetParameters});
        }
    }




    //Fetches all the card data to be displayed on the index page
    fetchCardData = async () => {
        this.setURL();
        try {
            const response = await axios.get(this.state.url, getAPIHeader());

            response.data.length >= 1 
            && Array.isArray(response.data)
                ?
                    this.setState({cardData: response.data})
                    :
                    this.setState({alternativeDisplayMessage: "No Card Data", cardData: []});
            }
            else {
                
            }
        } catch (error) {
            if (error.data == undefined) {
                this.setState({alternativeDisplayMessage: "No Card Data server errors", modalContent: emptyModalContent});
            }
            else {
                if (error.data.status === 401) {
                    axios.post(apiURL+'token/refresh',
                            { refreshToken : getRefreshToken() }
                    )
                    .then(response => {
                        setUserSession(response.data.token, response.data.refreshToken);
                    }).catch((error) => {
                        this.setState({alternativeDisplayMessage: "No Card Data", modalContent: emptyModalContent});
                    });
                }
                else {
                  //  window.location.replace('/HomeApp/logout');
                }
            }
        }
    }


    //gets the card form data so users can customize cards
    getCardDataForm = async (cardViewID) => {
        this.setState({modalLoading: cardViewID});
        const cardDataFormResponse = await axios.get(`${apiURL}card-data/card-sensor-form?cardViewID=${cardViewID}`, getAPIHeader())
    
        if (cardDataFormResponse.status === 200) {
            this.modalContent(cardDataFormResponse.data);
            this.setState({modalShow: true});
        } else {
            this.setState({alternativeDisplayMessage: 'Failed To Get Data'});
            alert("Failed Getting Form Please Try Again or Contact System Admin");
        }
        this.setState({modalLoading: false});
    }



    modalContent = (cardData) => {
        const cardColour = cardData.cardColour.colourID;
        const cardIcon = cardData.cardIcon;
        const cardViewID = cardData.cardViewID;
        const currentViewState = cardData.currentViewState;
        const sensorData = cardData.sensorData;

        const userCardViewSelections = cardData.userCardViewSelections;
        const userColourSelections = cardData.userColourSelections;
        const userIconSelections = cardData.iconSelection;

        this.setState({
            userSelectionData: {
                userCardViewSelections,
                userColourSelections,
                userIconSelections,
            }
        });

        this.setState({
            modalContent:{...this.state.modalContent,
                cardColour,
                cardIcon,
                cardViewID,
                currentViewState,
                sensorData,
            }
        });

        console.log('modal constent22', userIconSelections);
    }


    toggleModal = () => {
        this.setState({modalContent: emptyModalContent, modalShow: !this.state.modalShow});
        console.log(this.state.modalShow);
        this.setState({modalStatus: emptyModalStatus});
    }


    updateModalForm = (event, sensorType = null) => {
        const value = event.target.value;

        switch (event.target.name) {
            case "card-icon":
                const selectText = document.getElementById('icon-select');
                const option = selectText.options[selectText.selectedIndex];
                const currentModalData = this.state.modalContent;

                currentModalData.cardIcon.iconID = value;
                //this is to change the font awesome logo
                currentModalData.cardIcon.iconName = lowercaseFirstLetter(option.text);
                this.setState({modalContent:{...this.state.modalContent}});
                break;

            case "card-colour":
                this.setState({modalContent:{...this.state.modalContent, cardColour: value}});
                break;

            case "card-view-state":
                this.setState({modalContent:{...this.state.modalContent, currentState: value}});
                break;

            case sensorType+"-high-reading":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.sensorType === sensorType) {
                        currentModalData.highReading = value;
                        this.setState({modalContent:{...this.state.modalContent}});
                        break;
                    }
                  }
                break;

            case sensorType+"-low-reading":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.sensorType === sensorType) {
                        currentModalData.lowReading = value;
                        this.setState({modalContent:{...this.state.modalContent}});
                        break;
                    }
                  }
                break;

            case sensorType+"-const-record":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.sensorType === sensorType) {
                        currentModalData.constRecord = value === 'true';
                        this.setState({modalContent:{...this.state.modalContent}});
                        break;
                    }
                  }
                break;
        }
    }

    handleSubmissionModalForm = async (event) => {
        event.preventDefault();
        this.setState({modalStatus:{...this.state.modalStatus, modalSubmit: true, errors: []}});

        // console.log(this.state.modalContent);
        const jsonFormData = {
            'cardViewID' : this.state.modalContent.cardViewID,
            'cardColour' : this.state.modalContent.cardColour,
            'cardViewState' : this.state.modalContent.currentViewState.cardStateID,
            'cardIcon' : this.state.modalContent.cardIcon.iconID,
            'sensorData' : this.state.modalContent.sensorData,
            'constRecrod' : this.state.modalContent.constRecord,
        };


        try {
            const formSubmissionResult = await axios.put(apiURL+'card-data/update-card-view', jsonFormData, getAPIHeader());

            if (formSubmissionResult.status === 204) {
                this.setState({modalStatus:{...this.state.modalStatus, modalSubmit: false, submitSuccess: true, errors:[]}})
                setTimeout(() =>
                    this.toggleModal(), 1500
                );
            }
        } catch(error) {
            const badRequestErrors = (!error.data.errors.length > 1)
                ? ['something went wrong']
                : error.response.data.errors;

            if (error.response.status === 400) {
                this.setState({modalStatus:{...this.state.modalStatus, modalSubmit: false, errors: badRequestErrors}});            
            }

            if (error.response.status === 404) {
                this.setState({modalStatus:{...this.state.modalStatus,  modalSubmit: false,  errors: badRequestErrors}});
                this.toggleModal();
                alert('Could not handle request please try again');
            }

            if (error.response.status === 500) {
                if (error.response.data === undefined) {
                    alert('Please logout something went wrong');
                } else {
                    this.setState({modalStatus:{...this.state.modalStatus,  modalSubmit: false, errors: badRequestErrors}});
                }
            }
        }
    }

    render() {
        return (
            <div className="container-fluid">
                <div className="row">
                    <CardContext.Provider value={{
                        cardData: this.state.cardData,
                        getCardDataForm: this.getCardDataForm,
                        modalShow: this.state.modalShow,
                        modalLoading: this.state.modalLoading,
                        toggleModal: this.toggleModal,
                        modalContent: this.state.modalContent,
                        handleSubmissionModalForm: this.handleSubmissionModalForm,
                        updateModalForm: this.updateModalForm,
                        handleModalFormInput: this.handleModalFormInput,
                        errors: this.state.errors,
                        alternativeDisplayMessage: this.state.alternativeDisplayMessage,
                        userSelectionData: this.state.userSelectionData,
                        modalStatus: this.state.modalStatus,
                    }}>
                        {this.props.children}
                    </CardContext.Provider>
                </div>
            </div>
        )
    }
}
export default CardContextProvider;
