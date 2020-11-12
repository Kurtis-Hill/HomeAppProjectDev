import React, { Component, useContext } from 'react';
import { CardContext } from '../contexts/CardContexts';





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
                  <div className="row no-gutters align-items-center">
                    <div className="col mr-2">
                      <div className="d-flex font-weight-bold text text-uppercase mb-1">{cardData.sensorname}</div>
                      {cardData.t_tempreading !== null ? <div className={'card-font mb-0 font-weight-bold '+context.getSensorReadingStyle(cardData.t_hightemp, cardData.t_lowtemp, cardData.t_tempreading)}>Temperature: {cardData.t_tempreading}</div> : null}
                      {cardData.a_analogreading !== null ? <div className="card-font mb-0 font-weight-bold text-gray-800">Reading: {cardData.a_analogreading}</div> : null}
                      {cardData.h_humidreading !== null  ?  <div className={'card-font mb-0 font-weight-bold '+context.getSensorReadingStyle(cardData.h_highhumid, cardData.h_lowhumid, cardData.h_humidreading)}>Humidity: {cardData.h_humidreading}</div> : null}
                      {cardData.t_timez !== null ? <div className="card-font mb-0 text-gray-400">@{cardData.t_timez.date}</div> : null}
                      {cardData.a_timez !== null ? <div className="card-font mb-0 text-gray-400">@{cardData.a_timez.date}</div> : null}
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


