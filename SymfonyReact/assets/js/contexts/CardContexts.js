import React, { Component, createContext } from 'react'
import axios from 'axios';
import { getToken, getRefreshToken, setUserSession, lowercaseFirstLetter, apiURL, webappURL } from '../Utilities/Common';
import { DallasTemp, DHT, Soil } from '../Utilities/SensorsCommon';



export const CardContext = createContext();

// const emptyModalContent = {
//     submitSuccess: false, 
//     errors: [],
//     sensorHighReading: '', 
//     sensorLowReading: '', 
//     sensorType: '', 
//     constRecord: '', 
//     secondSensorID: null, 
//     secondSensorHighReading: '', 
//     secondSensorLowReading: '', 
//     secondSensorType: '', 
//     secondConstRecord: '', 
//     currentIcon: '', 
//     icons: [], 
//     iconID: '', 
//     currentColour: '', 
//     colours: [], 
//     states: [], 
//     currentState: '', 
//     cardViewID: null, 
//     modalSubmit: false
// };

const emptyModalContent = {
    submitSuccess: false, 
    errors: [],
    modalSubmit: false,
    cardViewID: null, 
    sensorData: [],
    cardIcon: [],
    cardColour: [],
    currentViewState: [],
    userIconSelections: [],
    userColourSelections: [],
    userCardViewSelections: [],
};


class CardContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            refreshTimer: 4000,
            cardData: [],
            modalShow: false,
            modalLoading: false,
            modalContent: emptyModalContent,
            url: '',
            errors: [],
            alternativeDisplayMessage: 'Loading...'
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
        }
        if (window.location.pathname === webappURL+'device') {
            const windowLocation = window.location.search;
            const urlParam = new URLSearchParams(windowLocation);

            const deviceName = urlParam.get('device-name');
            const deviceGroup = urlParam.get('device-group');
            const deviceRoom = urlParam.get('device-room');

            this.setState({url: cardAPI+"?device-name="+deviceName+"&device-group="+deviceGroup+"&device-room="+deviceRoom+"&view=device"});        
        }
    }


    //Fetches all the card data to be displayed on the index page
    fetchCardData = () => {
        axios.get(this.state.url, 
            { headers: {"Authorization" : `BEARER ${getToken()}`} }
        )
        .then(response => {
            // console.log('card response', response.data);
            if (response.data.length >= 1 ) {
                this.setState({cardData: response.data});
            }
            //console.log('card state', this.state.cardData);
        }).catch(error => {
            const err = error;
            console.log(err);
            if (err.status === 401) {
                axios.post(apiURL+'token/refresh', 
                    { refreshToken : getRefreshToken() } 
                )
                .then(response => {
                    setUserSession(response.data.token, response.data.refreshToken);
                });
            } else {
              //  window.location.replace('/HomeApp/logout');
            }
        })
        
    }


    //gets the card form data so users can customize cards
    getCardDataForm = (cardViewID) => {
        console.log('card view id', cardViewID);
        this.setState({modalLoading: true})
        axios.get(apiURL+'card-data/card-state-view-form?cardViewID='+cardViewID,
            { headers: {"Authorization" : `Bearer ${getToken()}`} })
        .then(response => {
            this.setState({modalLoading: false});
            this.modalContent(response.data);
            this.setState({modalShow: true});
        }).catch(error => {
            this.setState({modalLoading: false, alternativeDisplayMessage: 'Failed To Get Data'});
            alert("Failed Getting Form Please Try Again or Contact System Admin");
        })
    }


    modalContent = (cardData) => {
        const cardColour = cardData.cardColour;
        const cardIcon = cardData.cardIcon;
        const cardViewID = cardData.cardViewID;
        const currentViewState = cardData.currentViewState;
        const sensorData = cardData.sensorData;

        const userCardViewSelections = cardData.userCardViewSelections;
        const userColourSelections = cardData.userColourSelections;
        const userIconSelections = cardData.userIconSelections;
        const iconPreview = cardIcon;


        console.log( 'yep1', cardColour,
            cardIcon,
            cardViewID,
            currentViewState,
            sensorData,
            userCardViewSelections,
            userColourSelections,
            userIconSelections);

        this.setState({
            modalContent:{...this.state.modalContent, 
                cardColour,
                cardIcon,
                cardViewID,
                currentViewState,
                sensorData,
                userCardViewSelections,
                userColourSelections,
                userIconSelections,
                iconPreview,
            }
        });
        
        console.log('modal state', this.state.modalContent);
    }


    toggleModal = () => {
        this.setState({modalContent: emptyModalContent, modalShow: !this.state.modalShow});
    }

    
    updateModalForm = (event, sensorType = null) => {
        const value = event.target.value;

        switch (event.target.name) {
            case "cardIcon":
                const selectText = document.getElementById('icon-select');
                const option = selectText.options[selectText.selectedIndex];

                const currentModalData = this.state.modalContent;
                currentModalData.cardIcon.iconID = value;
                currentModalData.cardIcon.iconName = lowercaseFirstLetter(option.text);

                this.setState({modalContent:{...this.state.modalContent}});
                console.log('modalContent', this.state.modalContent);
                break;

            case "cardColour":
                this.setState({modalContent:{...this.state.modalContent, currentColour: value}});
                break;

            case "cardViewState":
                this.setState({modalContent:{...this.state.modalContent, currentState: value}});
                break;

            case sensorType+"HighReading":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.sensorType === sensorType) {
                        currentModalData.highReading = value;
                        this.setState({modalContent:{...this.state.modalContent}});
                        break;
                    }
                  }
                break;

            case sensorType+"LowReading":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.sensorType === sensorType) {
                        currentModalData.lowReading = value;
                        this.setState({modalContent:{...this.state.modalContent}});                    
                        break;
                    }
                  }
                break;

            case sensorType+"ConstRecord":
                for (const currentModalData of this.state.modalContent.sensorData) {
                    if (currentModalData.sensorType === sensorType) {
                        currentModalData.constRecord = value;
                        this.setState({modalContent:{...this.state.modalContent}});                    
                        break;
                    }
                  }
                break;
        }
    }

    handleSubmissionModalForm = (event) => {
        event.preventDefault();
        this.setState({modalContent:{...this.state.modalContent, modalSubmit: true}});

        const formData = new FormData(event.target);
        console.log('formdata', formData);
        formData.append('cardViewID', this.state.modalContent.cardViewID);
                
        const config = {     
            headers: { 'Content-Type': 'multipart/form-data' , "Authorization" : `BEARER ${getToken()}` }
        }
        
        axios.post(apiURL+'card-data/update-card-view', formData, config)
        .then(response => {
            this.setState({modalContent:{...this.state.modalContent, modalSubmit: false, submitSuccess: true}});

            setTimeout(() => 
                this.toggleModal(), 1500
            );
        })
        .catch(error => {
            const err = error.response;
            const errors = err.data.responseData;
            
            if (err.status === 400) {
                const badRequestErrors = (!errors.length > 1) 
                ? ['something went wrong']
                : errors;

                this.setState({modalContent:{...this.state.modalContent, modalSubmit: false, errors: badRequestErrors}});
            }

            if (err.status === 404) {
                this.setState({modalContent:{...this.state.modalContent,  modalSubmit: false, modalContent: emptyModalContent}});
                this.toggleModal();
                alert('Could not handle request please try again');
            }

            if (err.status === 500) {
                console.log('responsedata',err.data.responseData);
                const alertMessage = err.data.responseData !== undefined
                    ? err.data.responseData
                    : 'please try again or log out and try again';

                this.setState({modalContent:{...this.state.modalContent, modalSubmit: false}});
            }
        })
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
                    }}>
                        {this.props.children}
                    </CardContext.Provider>
                </div>  
            </div> 
        )
    }
}
export default CardContextProvider;
