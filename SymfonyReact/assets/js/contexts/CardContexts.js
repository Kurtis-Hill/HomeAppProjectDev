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
            console.log('modal state', this.state.modalContent);
            this.setState({modalShow: true});
        }).catch(error => {
            this.setState({modalLoading: false});
            alert("Failed Getting Form Please Try Again or Contact System Admin");
        })
    }


    modalContent = (cardData) => {
        const cardColour = cardData.cardColour;
        const cardIcon = cardData.cardIcon;
        const cardViewID = cardData.cardViewID;
        const currentViewState = cardData.currentViewState;
        const sensorData = cardData.sensorData;;

        const userCardViewSelections = cardData.userCardViewSelections;
        const userColourSelections = cardData.userColourSelections;
        const userIconSelections = userIconSelections;

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
            }
        });
        console.log('ye[');
        // const userData = response.cardSensorData;
        // const sensorType = userData.st_sensortype;

        // if (sensorType === DHT || DallasTemp) {
        //     var sensorHighReading = userData.t_highSensorReading;
        //     var sensorLowReading = userData.t_lowSensorReading;
        //     var constRecord = userData.constrecord;
        //     var sensorID = userData.t_tempid;

        //     if (sensorType === DHT) {
        //         var secondSensorHighReading = userData.h_highhumid;
        //         var secondSensorLowReading = userData.h_lowhumid;
        //         var secondConstRecord = userData.h_constrecord;
        //         var secondSensorID = userData.h_humidid;  
        //     }
        // }
        // if (sensorType === Soil) {
        //     var sensorHighReading = userData.a_highanalog;
        //     var sensorLowReading = userData.a_lowanalog;
        //     var constRecord = userData.a_constrecord;
        //     var sensorID = userData.h_analogid;
        // }

        // const cardViewID = userData.cv_cardviewid;

        // const sensorName = userData.sensorname;

        // const currentIcon = userData.i_iconname;
        // const iconID = userData.i_iconid;
        // const icons = response.icons;
        

        // const currentColour = userData.cc_colourid;
        // const colours = response.colours;

        // const currentState = userData.cs_cardstateid;
        // const states = response.states;

        // this.setState({modalContent:{...this.state.modalContent, sensorType, sensorName, sensorHighReading, sensorLowReading, secondSensorHighReading, secondSensorLowReading, secondSensorID, constRecord, secondConstRecord, sensorID, icons, currentIcon, iconID, currentColour, colours, cardViewID, currentState, states}});
    }


    toggleModal = () => {
        this.setState({modalContent: emptyModalContent, modalShow: !this.state.modalShow});
    }

    
    updateModalForm = (event) => {
        const value = event.target.value;

        switch (event.target.name) {
            case "icon":
                const selectText = document.getElementById('icon-select');
                const option = selectText.options[selectText.selectedIndex];
                this.setState({modalContent:{...this.state.modalContent, currentIcon: lowercaseFirstLetter(option.text), iconID: value}});
                break;

            case "cardColour":
                this.setState({modalContent:{...this.state.modalContent, currentColour: value}});
                break;

            case "cardViewState":
                this.setState({modalContent:{...this.state.modalContent, currentState: value}});
                break;

            case "constRecord":
                this.setState({modalContent:{...this.state.modalContent, constRecord: value}});
                break;

            case "secondConstRecord":
                this.setState({modalContent:{...this.state.modalContent, secondConstRecord: value}});
                break;

            case "firstSensorHighReading":
                this.setState({modalContent:{...this.state.modalContent, sensorHighReading: value}});
                break;

            case "firstSensorLowReading":
                this.setState({modalContent:{...this.state.modalContent, sensorLowReading: value}});
                break;

            case "secondSensorHighReading":
                this.setState({modalContent:{...this.state.modalContent, secondSensorHighReading: value}});
                break;

            case "secondSensorLowReading":
                this.setState({modalContent:{...this.state.modalContent, secondSensorLowReading: value}});
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
                        modalIcon: this.state.modalIcon,
                        updateModalForm: this.updateModalForm,
                        handleModalFormInput: this.handleModalFormInput,
                        errors: this.state.errors,
                    }}>
                        {this.props.children}
                    </CardContext.Provider>
                </div>  
            </div> 
        )
    }
}
export default CardContextProvider;
