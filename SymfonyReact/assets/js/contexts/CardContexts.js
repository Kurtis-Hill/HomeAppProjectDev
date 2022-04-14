import React, {Component, createContext} from 'react'
import axios from 'axios';

import { lowercaseFirstLetter, setUserSession } from '../Utilities/Common';
import { apiURL, webappURL } from '../Utilities/URLSCommon';
import { getAPIHeader, getRefreshToken } from '../Utilities/APICommon';

export const CardContext = createContext();

const emptyModalContent = {
    cardViewID: null,
    sensorData: [],
    cardIcon: [],
    cardColour: '',
    currentViewState: [],
    sensorId: null,
};

const emptyUserSelectionData = {
    userIconSelections: [],
    userColourSelections: [],
    userCardViewSelections: [],
}

const emptyModalStatus = {
    submitSuccess: false,
    errors: [],
    success: [],
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
        const cardAPI = `${apiURL}card-data/`;
        if (window.location.pathname === `${webappURL}index`) {
            const indexURL = `${cardAPI}index`;
            this.setState({url:  indexURL});
            return;
        }
        const windowLocation = window.location.search;
        const urlParam = new URLSearchParams(windowLocation);

        if (window.location.pathname ===`${webappURL}device`) {
            const deviceName = urlParam.get('device-id');
            const deviceURL = `${cardAPI}device-cards/${deviceName}`;
            this.setState({url:  deviceURL});
            return;
        }
        if (window.location.pathname === `${webappURL}room`) {
            const roomName = urlParam.get('room-id');
            const roomURL = `${cardAPI}room-cards/${roomName}`;
            this.setState({url:  roomURL});
            return;
        }
    }

    //Fetches all the card data to be displayed on the index page
    fetchCardData = async () => {
        this.setURL();
        try {
            const response = await axios.get(this.state.url, getAPIHeader());

            Array.isArray(response.data.payload) 
            && response.data.payload.length >= 1
                ? this.setState({cardData: response.data.payload})
                : this.setState({alternativeDisplayMessage: "No Card Data", cardData: []});    
        } catch (error) {
            if (error.data === undefined) {
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
                   window.location.replace('/HomeApp/logout');
                }
            }
        }
    }

    //gets the card form data so users can customize cards
    getCardDataForm = async (cardViewID) => {
        this.setState({modalLoading: cardViewID});
        try {
            const cardDataFormResponse = await axios.get(`${apiURL}card-form-data/get/${cardViewID}`, getAPIHeader())
    
            if (cardDataFormResponse.status === 200) {
                this.modalContent(cardDataFormResponse.data.payload);
                this.setState({modalShow: true});
            } else {
                this.setState({alternativeDisplayMessage: 'unexpected response, try  logging out and back in again'});
            }
        } catch (error) {
            this.setState({alternativeDisplayMessage: error.data.payload[0]});
            this.setState({modalLoading: false});
        }

        this.setState({modalLoading: false});
    }

    modalContent = (cardData) => {
        const sensorId = cardData.sensorId;
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
            modalContent:{
                ...this.state.modalContent,
                sensorId,
                cardColour,
                cardIcon,
                cardViewID,
                currentViewState,
                sensorData,
            }
        });
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
                    if (currentModalData.readingType === sensorType) {
                        currentModalData.highReading = value;
                        this.setState({modalContent:{...this.state.modalContent}});
                        break;
                    }
                  }
                break;

            case sensorType+"-low-reading":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.readingType === sensorType) {
                        currentModalData.lowReading = value;
                        this.setState({modalContent:{...this.state.modalContent}});
                        break;
                    }
                  }
                break;

            case sensorType+"-const-record":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.readingType === sensorType) {
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
        this.setState({modalStatus:{...this.state.modalStatus, modalSubmit: true, errors: [], success: []}});

        const cardFormData = {
            'cardColour' : parseInt(this.state.modalContent.cardColour),
            'cardViewState' : parseInt(this.state.modalContent.currentViewState.cardStateID),
            'cardIcon' : parseInt(this.state.modalContent.cardIcon.iconID),
        };

        const sensorBoundaryUpdateData = {
            'sensorData' : this.state.modalContent.sensorData,
        }

        try {
            const cardRequestResponse = await axios.put(`${apiURL}card-form-data/update/${this.state.modalContent.cardViewID}`, cardFormData, getAPIHeader());
            const sensorReadingUpdateResponse = await axios.put(`${apiURL}sensor/${this.state.modalContent.sensorId}/boundary-update`, sensorBoundaryUpdateData, getAPIHeader());
            
            if (cardRequestResponse.status === 202 && sensorReadingUpdateResponse.status === 202) {
                this.setState({modalStatus:{...this.state.modalStatus, modalSubmit: false, submitSuccess: true, errors:[]}})
                setTimeout(() =>
                    this.toggleModal(), 1500
                );
            } else {            
                const sensorBoundaryRequestErrors = Array.isArray(sensorReadingUpdateResponse.data.errors)
                ? sensorReadingUpdateResponse.data.errors
                : null;

                const sensorBoundaryRequestSuccess = Array.isArray(sensorReadingUpdateResponse.data.payload)
                ? sensorReadingUpdateResponse.data.payload
                : null;


                const cardRequestErrors = Array.isArray(cardRequestResponse.data.errors)
                ? cardRequestResponse.data.errors
                : null;

                const cardRequestSuccess = Array.isArray(cardRequestResponse.data.payload)
                ? cardRequestResponse.data.payload
                : null;

                this.setState({modalStatus:{
                    ...this.state.modalStatus,
                    modalSubmit: false,
                    submitSuccess: false,
                    errors: sensorBoundaryRequestErrors.concat(cardRequestErrors),
                    success: sensorBoundaryRequestSuccess.concat(cardRequestSuccess),
                }})
            }
        } catch(error) {
            const badRequestErrors = !Array.isArray(error.response.data.errors) || !error.response.data.errors.length > 1
                ? ['Something went wrong, unexpected response']
                : error.response.data.errors;

            this.setState({
                modalStatus:{
                    ...this.state.modalStatus,
                    modalSubmit: false,
                    errors: badRequestErrors
                }
            });
            if (error.response.status === 500) {
                alert('Please logout something went wrong');
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
