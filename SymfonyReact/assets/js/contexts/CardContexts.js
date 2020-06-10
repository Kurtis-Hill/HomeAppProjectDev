import React, { Component, createContext } from 'react'
import ReactDOM from "react-dom";
import axios from 'axios';
import { getToken } from '../Utilities/Common';
import { getUser } from '../Utilities/Common';
import  { CardModal }  from '../components/CardFormModal.jsx';

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
            this.modalContent(response.data, id);
            this.setState({modalShow: true});
            console.log(response.data);
        }).catch(error => {
            console.log(error);
        })
    }

    // getCardDataForm = (id, room, sensorname) => {
    //     console.log('clickedd');
    //     // CardModal(id, room, sensorname);
    // }

    modalContent = (response, id) => {

        const formURL = 'api/CardData/cardviewform&id='+id;
        console.log("response", response);
        const userData = response[0];

        if (userData.t_tempid != null) {
            var sensorHighReading = userData.t_hightemp;
            var sensorLowReadings = userData.t_lowtemp;
            var constRecord = userData.t_constrecord ? "selected" : null;
            var sensorID = userData.t_tempid;

            if (userData.h_humidid !== undefined) {
                var humidHighReading = userData.h_highhumid;
                var sensorLowReading = userData.h_lowhumid;
                var sensorTwoID = userData.h_humidid;
            }
        }

        if (userData.a_analogid != null) {
            var sensorHighReading = userData.h_highanalog;
            var sensorLowReadings = userData.h_lowanalog;
            var sensorID = userData.h_analogid;
        }

        const sensorName = userData.sensorname;

        const cardColour = userData.cc_colour;
        const allCardColours = userData.cardColours

        const currentIcon = userData.i_iconname;
        const icons = response[1];

        const currentColour = this.capitalizeFirstLetter(userData.cc_colour);
        const colours = response[2];

        const currentCardView = this.capitalizeFirstLetter(userData.cs_state);
        const states = response[3];

        this.setState({modalContent: 
            <form method="post" action={formURL} >
                <div className="modal-header">
                    <h5 className="modal-title" id="exampleModalLabel">Change {sensorName}'s Sensor Details</h5>
                        <button className="close" onClick={() => {this.toggleModal()}} type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                            {/* <span onClick={context.toggleModal()} aria-hidden="true">×</span> */}
                        </button>
                </div>
                <div className="modal-body">
                    <label className="modal-element large font-weight-bold">High Reading</label>
                    <input type="text" name="highReading" className="form-control" placeholder={sensorHighReading}></input>
                
                    <label className="modal-element large font-weight-bold">Low Reading</label>
                    <input type="text" name="lowReading" className="form-control" placeholder={sensorLowReadings}></input>

                    <label className="modal-element large font-weight-bold">Icon</label>
                    <br />
                    <select name="cardiconid" defaultValue={this.capitalizeFirstLetter(currentIcon)} className="form-space">
                        {icons.map((icons, index) => (
                            <option value={icons.i_iconid} key={icons.i_iconid}>{this.capitalizeFirstLetter(icons.i_iconname)}</option>
                            ))}
                    </select>
                    <i className={"fas fa-2x text-gray-300 modal-icon fa-"+currentIcon}></i>
                    <br />

                    <label className="modal-element large font-weight-bold">Card Colour</label>
                    <select name="cardcolourid" defaultValue={currentColour} className="form-control">
                        {colours.map((colours, index) => (
                        <option value={colours.c_colourid} key={colours.c_colourid}>{colours.c_shade}</option>
                        ))}
                    </select>

                    <label className="modal-element large font-weight-bold">Card View</label>
                    <select name="cardSensorStateOne" defaultValue={currentCardView} className="form-control">
                        {states.map((states, index) => (
                        <option value={states.cs_cardstateid} key={states.cs_cardstateid}>{this.capitalizeFirstLetter(states.cs_state)}</option>
                        ))}
                    </select>

                    <label className="modal-element large font-weight-bold">Constantly Record Data</label>
                    <select className="form-control">
                        <option value="0" key="no" selected={constRecord}>No</option>
                        <option value="1" key="yes">Yes</option>
                    </select>
                </div>
                <div className="modal-footer">
                    <button className="btn btn-secondary" type="button" onClick={() => {context.toggleModal()}} data-dismiss="modal">Cancel</button>
                    <button className="btn btn-primary" name="submit" action="submit">Submit</button>
                </div>
            </form>
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
        return currentReading >= highReading ? 'text-red' : currentReading <= lowReading ? 'text-blue' : 'text-gray-800';
    }
    
    //Checks to see if humidity is set in the tempHumid array
    isHumidityAvalible = (object) => {
        return object.h_humidreading !== null ?
        <div className={'h5 mb-0 font-weight-bold '+this.getSensorReadingStyle(object.h_highhumid, object.h_lowhumid, object.h_humidreading)}>Humidity: {
            object.h_humidreading
           }</div> : object.h_humidreading;
    }

    toggleModal = () => {
        this.setState({modalShow: !this.state.modalShow});
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
