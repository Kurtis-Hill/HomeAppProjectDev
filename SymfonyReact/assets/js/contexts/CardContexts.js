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
            state: 'setting',
            refreshTimer: 4000,
            tempHumid: [],
            analog: [],
        };
        console.log('Token from storage', token)
    }


//     axiosToken = () => {
//         const token = sessionStorage.getItem('token');
//         if (token) {
//             axios.defaults.headers.common["Bearer"] = token;
//         } else {
//             axios.defaults.headers.common['Authorization'] = null;
//         }
//    };

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
    getCardDataForm = (id) => {
        console.log("pressed" + id);
        axios.get('/HomeApp/index/CardViewForm?&id='+id)
        .then(response => {
            // let hey = ReactDOM.findDOMNode(<div className="modal fade" id="logoutModal" tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>).getElementsByClassName('aria-hidden');
            console.log(response.data);
        }).catch(error => {
            console.log(error);
        })
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
                    }}>
                        {this.props.children}
                    </CardContext.Provider>
                </div>  
            </div> 
        )
    }
}


export default CardContextProvider;
