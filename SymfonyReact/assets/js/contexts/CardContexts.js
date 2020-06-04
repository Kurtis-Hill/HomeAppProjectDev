import React, { Component, createContext } from 'react'
import ReactDOM from "react-dom";
import axios from 'axios';
import { getToken } from '../Utilities/Common';
import { getUser } from '../Utilities/Common';

export const CardContext = createContext();


const token = sessionStorage.getItem('token');

class CardContextProvider extends Component {
    constructor(props) {
        super(props);
        this.state = {
            refreshTimer: 4000,
            tempHumid: [],
            analog: [],
            modalShow: false,
            modalContent: '',
            modalLoading: false,
        };
        console.log('Token from storage', token)
    }


    componentDidMount() {
        //if HomeApp/index fetchIndexCardData if Rooms fetch cardsForRoom()
        // this.axiosToken();
      
        this.fetchIndexCardData();
    }

    componentDidUpdate(prevProps, preState) {
        // console.log('prev state', preState);
        // console.log('prev props', prevProps);
    }


    //Fetches all the card data to be displayed on the index page
    fetchIndexCardData = () => {
        axios.get('/HomeApp/api/CardData/index', 
        { headers: {"Authorization" : `Bearer ${token}`} })
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

    //gets the card form data so users can customize cards
    getCardDataForm = (id, room, sensorname) => {
        this.setState({modalLoading: true})
        console.log("pressed" + id);
        axios.get('/HomeApp/api/CardData/cardviewform&id='+id,
        { headers: {"Authorization" : `Bearer ${token}`} })
        .then(response => {
            this.setState({modalLoading: false});
            this.modalContent(response.data);
            this.setState({modalShow: true});
            console.log(response.data);
        }).catch(error => {
            console.log(error);
        })
    }


    modalContent = (response) => {

        console.log("response", response);
        const userData = response[0];

        if (userData.t_tempid != null) {
            var sensorHighReading = userData.t_hightemp;
            var sensorLowReadings = userData.t_lowtemp;
            var constRecord = userData.t_constrecord ? "selected" : null;
            const sensorID = userData.t_tempid;

            if (userData.h_humidid !== undefined) {
                const humidHighReading = userData.h_highhumid;
                const sensorLowReading = userData.h_lowhumid;
                const sensorTwoID = userData.h_humidid;
            }
        }

        if (userData.a_analogid != null) {
            var sensorHighReading = userData.h_highanalog;
            var sensorLowReadings = userData.h_lowanalog;
            const sensorID = userData.h_analogid;
        }

        const sensorName = userData.sensorname;
        const cardColour = userData.cc_colour;
        const allCardColours = userData.cardColours

        const currentIcon = this.capitalizeFirstLetter(userData.i_iconname);
        const icons = response[1];

        const currentColour = this.capitalizeFirstLetter(userData.cc_colour);
        const colours = response[2];


        this.setState({modalContent: 
            <div>
                <div className="modal-header">
                <h5 className="modal-title" id="exampleModalLabel">Change {sensorName}'s Sensor Details</h5>
                    <button className="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        {/* <span onClick={context.toggleModal()} aria-hidden="true">×</span> */}
                    </button>
                </div>
                <div className="modal-body">
                    <label>High Reading</label>
                    <input type="text" name="highReading" className="form-control" placeholder={sensorHighReading}></input>
                
                    <label>Low Reading</label>
                    <input type="text" name="lowReading" className="form-control" placeholder={sensorLowReadings}></input>

                    <label>Icon</label>
                    <select defaultValue={currentIcon} className="form-control">
                        {icons.map((icons, index) => (
                        <option key={index}>{this.capitalizeFirstLetter(icons.i_iconname)}</option>
                        ))}
                    </select>
        
                    <label>Card Colour</label>
                    <select defaultValue={currentColour} className="form-control">
                        {colours.map((colours, index) => (
                        <option key={colours.colourid}>{colours.c_shade}</option>
                        ))}
                    </select>

                    <label>Constantly Record Data</label>
                    <select>
                        <option selected={constRecord}>No</option>
                        <option>Yes</option>
                    </select>

                </div>
            </div>
        })
    }

    capitalizeFirstLetter = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1);
      }


    //Send data from the card modal to the backend
    sendCardFormData = () => {

    }

    //Changes the style of the card text if the reading is above or below high-low readings in DB
    getSensorReadingStyle = (highReading, lowReading, currentReading) => {
        const fontStyle= 'h5 mb-0 font-weight-bold ';
        const style = currentReading >= highReading ? fontStyle+'text-red' : currentReading <= lowReading ? fontStyle+'text-blue' : fontStyle+'text-gray-800';
       
        return style;
    }
    
    //Checks to see if humidity is set in the tempHumid array
    isHumidityAvalible = (object) => {
        const humid = object.h_humidreading !== null ?
        <div className={this.getSensorReadingStyle(object.h_highhumid, object.h_lowhumid, object.h_humidreading)}>Humidity: {
            object.h_humidreading
           }</div> : object.h_humidreading;
        //needs changing ternary to not return null second half maybe ?? or ?:
        return humid;
        
    }

    toggleModal = () => {

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
                        modalShow: this.state.modalShow,
                        modalContent: this.state.modalContent,
                        modalLoading: this.state.modalLoading,
                        toggleModal: this.toggleModal,
                    }}>
                        {this.props.children}
                    </CardContext.Provider>
                </div>  
            </div> 
        )
    }
}


export default CardContextProvider;
