import React, { Component, createContext } from 'react'
import ReactDOM from "react-dom";
import axios from 'axios';
import { getToken } from '../Utilities/Common';
import { getUser } from '../Utilities/Common';


export const CardContext = createContext();

const emptyModalContent = {sensorType: '', secondSensorType: '', currentIcon: '', icons: [], currentColour: '', colours: [], states: [], currentState: '', constRecord: '', secondConstRecord: '', cardViewID: '', modalSubmit: false};

class CardContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            refreshTimer: 4000,
            tempHumid: [],
            analog: [],
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
            console.log('CardData', response.data);
            this.setState({
                tempHumid:response.data.tempHumid,
                analog:response.data.analog,
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
    
    //Checks to see if humidity is set in the tempHumid array
    isHumidityAvalible = (object) => {
        return object.h_humidreading !== null ?
        <div className={'h5 mb-0 font-weight-bold '+this.getSensorReadingStyle(object.h_highhumid, object.h_lowhumid, object.h_humidreading)}>Humidity: {object.h_humidreading}</div> 
        : null;
    }

    //gets the card form data so users can customize cards
    getCardDataForm = (id, room, sensorname) => {
        this.setState({modalLoading: true})
        console.log("pressed" + id);
        axios.get('/HomeApp/api/CardData/cardviewform&id='+id,
        { headers: {"Authorization" : `Bearer ${getToken()}`} })
        .then(response => {
            this.setState({modalLoading: false});
            this.modalContent(response.data);
            this.setState({modalShow: true});
        }).catch(error => {
            console.log(error);
            this.setState({modalLoading: false});
            alert("Failed Getting Form Please Try Again");
        })
    }

    // getCardDataForm = (id, room, sensorname) => {
    //     console.log('clickedd');
    //     // CardModal(id, room, sensorname);
    // }

    modalContent = (response) => {

      //  console.log('new token', getToken());
        const userData = response.cardSensorData;

        let sensorType;
        let secondSensorType;

        if (userData.t_tempid !== null) {
            var sensorHighReading = userData.t_hightemp;
            var sensorLowReadings = userData.t_lowtemp;
            var constRecord = userData.t_constrecord ? "Yes" : 'No';
            var sensorID = userData.t_tempid;
            sensorType = "Temperature";

            if (userData.h_humidid !== undefined) {
                var secondSensorHighReading = userData.h_highhumid;
                var secondSensorLowReading = userData.h_lowhumid;
                var secondConstRecord = userData.h_constrecord ? "Yes" : 'No';
                var secondSensorID = userData.h_humidid;
                secondSensorType = "Humidity";
            }
        }

        if (userData.a_analogid !== null) {
            var sensorHighReading = userData.h_highanalog;
            var sensorLowReadings = userData.h_lowanalog;
            var constRecord = userData.a_constrecord ? "Yes" : 'No';
            var sensorID = userData.h_analogid;
            sensorType = "Analog";
        }

        const cardViewID = userData.cv_cardviewid;

        const sensorName = userData.sensorname;

     
        const currentIcon = userData.i_iconname;
        const icons = response.icons;

        const currentColour = this.capitalizeFirstLetter(userData.cc_shade);
        const colours = response.colours;

        const currentState = this.capitalizeFirstLetter(userData.cs_state);
        const states = response.states;

        this.setState({modalContent:{ sensorType, secondSensorType, sensorName, sensorHighReading, sensorLowReadings, secondSensorHighReading, secondSensorLowReading, secondSensorID, constRecord, secondConstRecord, sensorID, icons, currentIcon, currentColour, colours, cardViewID, currentState, states}});

        console.log('modal content', currentColour);
        // console.log('icons', this.state.modalContent.)
    }


    capitalizeFirstLetter = (string) => {
        if (string != undefined) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        return null;
    }

    lowercaseFirstLetter = (string) => {
        if (string != undefined) {
            return string.charAt(0).toLowerCase() + string.slice(1);
        }
        return null;
    }


    toggleModal = () => {
        this.setState({modalShow: !this.state.modalShow});
        if (this.state.modalShow === false) {
            this.setState({modalContent: emptyModalContent});
        }
    }

    updateModalForm = (e) => {
        const value = e.target.value;

        switch(e.target.name) {
            case "icon":
                this.setState({modalContent:{...this.state.modalContent, currentIcon: this.lowercaseFirstLetter(value)}});
                break;

            case "colour":
                this.setState({modalContent:{...this.state.modalContent, currentColour: this.lowercaseFirstLetter(value)}});
                break;

            case "card-view":
                this.setState({modalContent:{...this.state.modalContent, currentState: this.lowercaseFirstLetter(value)}});
                break;

            case "const-record":
                this.setState({modalContent:{...this.state.modalContent, constRecord: this.lowercaseFirstLetter(value)}});
                break;

            case "second-const-record":
                this.setState({modalContent:{...this.state.modalContent, secondConstRecord: this.lowercaseFirstLetter(value)}});
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
        console.log('form update', e.target.name, value);
    }

    //  <--!!! TODO WORKING ON THIS !!!-->
    handleModalForm = (e) => {
        this.setState({modalContent:{...this.state.modalContent, modalSubmit: true}});
        e.preventDefault();
        

        const formData = new FormData(e.target);

        formData.append('cardViewID', this.state.modalContent.cardViewID);
                
        console.log('form', this.state.modalContent.modalSubmit);

        const config = {     
            headers: { 'content-type': 'multipart/form-data' }
        }


        axios.post('/HomeApp/api/CardData/cardviewform&id='+this.state.modalContent.cardViewID, formData, config,
            { headers: {"Authorization" : `BEARER ${getToken()}`} })
        .then(response => {
            console.log('card modal form resposne', response);
        })
    }

    handleModalFormInput = (e) => {

    }

    
    render() {
        return (
            <div className="container-fluid">
                <div className="row">
                    <CardContext.Provider value={{
                        tempHumid: this.state.tempHumid,
                        analog: this.state.analog,
                        getCardDataForm: this.getCardDataForm,
                        getSensorReadingStyle: this.getSensorReadingStyle,
                        isHumidityAvalible: this.isHumidityAvalible,
                        capitalizeFirstLetter: this.capitalizeFirstLetter,
                        modalShow: this.state.modalShow,
                        modalLoading: this.state.modalLoading,
                        toggleModal: this.toggleModal,
                        modalContent: this.state.modalContent,
                        handleModalForm: this.handleModalForm,
                        modalIcon: this.state.modalIcon,
                        updateModalForm: this.updateModalForm,
                        handleModalFormInput: this.handleModalFormInput,
                        //lowercaseFirstLetter: this.lowercaseFirstLetter
                    }}>
                        {this.props.children}
                    </CardContext.Provider>
                </div>  
            </div> 
        )
    }
}


export default CardContextProvider;
