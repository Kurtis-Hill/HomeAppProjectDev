import React, { useContext } from 'react';
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
      {
        context.errors.length > 1 
        ? <h1>{context.cardData.errors}</h1> 
        : 
          <React.Fragment>
            {
              context.cardData.length >= 1 ?
                context.cardData.map((cardData) => (
                  <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(cardData.cardViewID)}} key={cardData.cardViewID}>
                    <div className={"shadow h-100 py-2 card border-left-"+cardData.cardColour}>
                      <div className="card-body hover">
                        <div style={{ position: "absolute", top: '2%', right: '5%'}}>{cardData.sensorType}</div>
                          <div className="row no-gutters align-items-center">
                            <div className="col mr-2">
                              <div className="d-flex font-weight-bold text text-uppercase mb-1">{cardData.sensorName}</div>
                              <div className="d-flex text text-uppercase mb-1">{cardData.sensorRoom}</div>
                              {
                              cardData.sensorData.length >= 1 
                                ? cardData.sensorData.map((sensorData, index) => (
                                    <React.Fragment>
                                      <div className={'card-font mb-0 font-weight-bold '+senorReadingStyle(sensorData.highReading, sensorData.lowReading, sensorData.currentReading)} key={index}>
                                        {sensorData.sensorType}: {sensorData.currentReading}{sensorData.readingSymbol}
                                      </div>
                                      <div className="card-font mb-0 text-gray-400">@{sensorData.time}</div>
                                    </React.Fragment>
                                  ))
                                : <p>No Sensor Data</p>
                              }
                            </div>
                            <div className="col-auto">
                              <i className={"fas fa-2x text-gray-300 fa-"+cardData.cardIcon}></i>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div> 
                  ))
              : 
              <div>{context.alternativeDisplayMessage}</div>
            }
          </React.Fragment>
      }
    </React.Fragment>
  )
}


 
 export default cardRender;


