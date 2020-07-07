import React, { Component, createContext } from 'react'
import axios from 'axios';
import { getToken } from '../Utilities/Common';
import { capitalizeFirstLetter } from '../Utilities/Common';
import { lowercaseFirstLetter } from '../Utilities/Common';


export const CardContext = createContext();

const emptyModalContent = {secondSensorID: null, sensorHighReading: null, sensorLowReading: null, secondSensorHighReading: '', secondSensorLowReading: '', sensorType: '', secondSensorType: '', currentIcon: '', icons: [], iconID: '', currentColour: '', colours: [], states: [], currentState: '', constRecord: '', secondConstRecord: '', cardViewID: null, modalSubmit: false};

class CardContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            refreshTimer: 4000,
            cardData: [],
            modalShow: false,
            modalLoading: false,
            modalContent: emptyModalContent,
        };
    }

    componentDidMount() {
        //if HomeApp/index fetchIndexCardData if Rooms fetch cardsForRoom()      
        this.fetchIndexCardData();
    }

    componentDidUpdate(prevProps, preState) {
        // console.log('prev state', preState);
        // console.log('prev props', prevProps);
    }

    componentWillUnmount() {
        // clearInterval(this.fetchIndexCardData);
      }


    //Fetches all the card data to be displayed on the index page
    fetchIndexCardData = () => {
        axios.get('/HomeApp/api/CardData/index', 
        { headers: {"Authorization" : `Bearer ${getToken()}`} })
        .then(response => {
            this.setState({
                cardData:response.data.sensorData,
            })
            setTimeout(() => this.fetchIndexCardData(), this.state.refreshTimer);
        }).catch(error => {
            console.log(error);
        })
    }

    //Changes the style of the card text if the reading is above or below high-low readings in DB
    getSensorReadingStyle = (highReading, lowReading, currentReading) => {
        return currentReading >= highReading ? 'text-red' : currentReading <= lowReading ? 'text-blue' : 'text-gray-800';
    }

    //gets the card form data so users can customize cards
    getCardDataForm = (cardViewID) => {
        this.setState({modalLoading: true})
        axios.get('/HomeApp/api/CardData/cardviewform&id='+cardViewID,
        { headers: {"Authorization" : `Bearer ${getToken()}`} })
        .then(response => {
            this.setState({modalLoading: false});
            this.modalContent(response.data);
            this.setState({modalShow: true});
        }).catch(error => {
            console.log(error);
            this.setState({modalLoading: false});
            alert("Failed Getting Form Please Try Again or Contact System Admin");
        })
    }


    modalContent = (response) => {
        const userData = response.cardSensorData;
         console.log('userData', userData);
         const sensorType = userData.st_sensortype;

        if (sensorType === "Temp") {

        }
        if (sensorType === "TempHumid") {
             
        }
        if (sensorType === "Analog") {
             
        }
        if (userData.t_tempid !== null) {
            var sensorHighReading = userData.t_hightemp;
            var sensorLowReading = userData.t_lowtemp;
            var constRecord = userData.t_constrecord;
            var sensorID = userData.t_tempid;
            // var sensorType = "Temperature";

            if (userData.h_humidid !== undefined) {
                var secondSensorHighReading = userData.h_highhumid;
                var secondSensorLowReading = userData.h_lowhumid;
                var secondConstRecord = userData.h_constrecord;
                var secondSensorID = userData.h_humidid;
                 var secondSensorType = "Humidity";
            }
        }

        if (userData.a_analogid !== null) {
            var sensorHighReading = userData.a_highanalog;
            var sensorLowReading = userData.a_lowanalog;
            var constRecord = userData.a_constrecord ? "Yes" : 'No';
            var sensorID = userData.h_analogid;
          //  var sensorType = "Analog";
        }

        const cardViewID = userData.cv_cardviewid;

        const sensorName = userData.sensorname;

        const currentIcon = userData.i_iconname;
        const iconID = userData.i_iconid;
        const icons = response.icons;
        

        const currentColour = userData.cc_colourid;
        const colours = response.colours;

        const currentState = userData.cs_cardstateid;
        const states = response.states;

        this.setState({modalContent:{sensorType, secondSensorType, sensorName, sensorHighReading, sensorLowReading, secondSensorHighReading, secondSensorLowReading, secondSensorID, constRecord, secondConstRecord, sensorID, icons, currentIcon, iconID, currentColour, colours, cardViewID, currentState, states}});
        console.log('moda content', this.state.modalContent);
    }



    toggleModal = () => {
        this.setState({modalShow: !this.state.modalShow});
    }

    updateModalForm = (event) => {
        const value = event.target.value;

        switch(event.target.name) {
            case "icon":
                const selectText = document.getElementById('icon-select');
                const opt = selectText.options[selectText.selectedIndex];
                this.setState({modalContent:{...this.state.modalContent, currentIcon: lowercaseFirstLetter(opt.text), iconID: value}});
                break;

            case "colour":
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

            case "highReading":
                this.setState({modalContent:{...this.state.modalContent, sensorHighReading: value}});
                break;

            case "lowReading":
                this.setState({modalContent:{...this.state.modalContent, sensorLowReading: value}});
                break;

            case "secondHighReading":
                this.setState({modalContent:{...this.state.modalContent, secondSensorHighReading: value}});
                break;

            case "secondLowReading":
                this.setState({modalContent:{...this.state.modalContent, secondSensorLowReading: value}});
                break;
        }
        console.log('changed state', this.state.modalContent);
    }

    //  <--!!! TODO WORKING ON THIS !!!-->
    handleSubmissionModalForm = (event) => {
        event.preventDefault();
        this.setState({modalContent:{...this.state.modalContent, modalSubmit: true}});
        const formData = new FormData(event.target);
        console.log('subbmite bvalues', formData.value);

        formData.append('cardViewID', this.state.modalContent.cardViewID);
                
        const config = {     
            headers: { 'Content-Type': 'multipart/form-data' , "Authorization" : `BEARER ${getToken()}` }
        }
        
        axios.post('/HomeApp/api/CardData/updatecardview',formData, config)
        .then(response => {
            this.setState({modalContent:{...this.state.modalContent, modalSubmit: false}});
            this.setState({modalContent: emptyModalContent});
            this.toggleModal();
        })
    }

    
    render() {
        return (
            <div className="container-fluid">
                <div className="row">
                    <CardContext.Provider value={{
                        cardData: this.state.cardData,
                        getCardDataForm: this.getCardDataForm,
                        getSensorReadingStyle: this.getSensorReadingStyle,
                        isHumidityAvalible: this.isHumidityAvalible,
                        modalShow: this.state.modalShow,
                        modalLoading: this.state.modalLoading,
                        toggleModal: this.toggleModal,
                        modalContent: this.state.modalContent,
                        handleSubmissionModalForm: this.handleSubmissionModalForm,
                        modalIcon: this.state.modalIcon,
                        updateModalForm: this.updateModalForm,
                        handleModalFormInput: this.handleModalFormInput,
                    }}>
                        {this.props.children}
                    </CardContext.Provider>
                    <CardModal.Provider>

                    </CardModal.Provider>

                </div>  
            </div> 
        )
    }
}


export default CardContextProvider;
