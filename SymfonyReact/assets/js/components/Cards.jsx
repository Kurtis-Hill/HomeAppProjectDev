import React, { Component, useContext } from 'react';
import { CardContext } from '../contexts/CardContexts';

const senorReadingStyle = (highReading, lowReading, currentReading) => {
  return (currentReading >= highReading) 
    ? 'text-red' 
    : (currentReading <= lowReading) 
      ? 'text-blue' 
      : 'text-gray-800';
}

const cardRender = () => {

  const context = useContext(CardContext);
  return ( 
    <React.Fragment>
      {context.errors.length > 1 ? <h1>{context.cardData.errors}</h1> : 
        <React.Fragment>
          {context.cardData.map((cardData, index) => (
            <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(cardData.cardviewid)}} key={index}>
              <div className={"shadow h-100 py-2 card border-left-"+cardData.colour}>
                <div className="card-body hover">
                <div style={{ position: "absolute", top: '2%', right: '5%'}}>{cardData.sensortype}</div>
                  <div className="row no-gutters align-items-center">
                    <div className="col mr-2">
                      <div className="d-flex font-weight-bold text text-uppercase mb-1">{cardData.sensorname}</div>
                      <div className="d-flex text text-uppercase mb-1">{cardData.room}</div>
                      {cardData.tempReading !== null ? <div className={'card-font mb-0 font-weight-bold '+senorReadingStyle(cardData.highTempReading, cardData.lowTempReading, cardData.tempReading)}>Temperature: {cardData.tempReading}</div> : null}
                      {cardData.analogReading !== null ? <div className={'card-font mb-0 font-weight-bold '+senorReadingStyle(cardData.analogReading, cardData.lowAnalogReading, cardData.highAnalogReading)}>Analog Reading: {cardData.analogReading}</div> : null}
                      {cardData.humidReading !== null  ?  <div className={'card-font mb-0 font-weight-bold '+senorReadingStyle(cardData.highHumidReading, cardData.lowHumidReading, cardData.humidReading)}>Humidity: {cardData.humidReading}</div> : null}
                      {cardData.tempTime !== null ? <div className="card-font mb-0 text-gray-400">@{cardData.tempTime.date}</div> : null}
                      {cardData.analogTime !== null ? <div className="card-font mb-0 text-gray-400">@{cardData.analogTime.date}</div> : null}
                    </div>                  
                    <div className="col-auto">
                      <i className={"fas fa-2x text-gray-300 fa-"+cardData.iconname}></i>
                    </div>
                  </div>
                </div>
              </div>
            </div> 
            ))
          }
        </React.Fragment>
      }
    </React.Fragment>
  )
}


 
 export default cardRender;


