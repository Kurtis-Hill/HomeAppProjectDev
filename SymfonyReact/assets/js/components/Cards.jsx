import React, { Component, useContext, useState } from 'react'
import { CardContext } from '../contexts/CardContexts'

const cardRender = () => {

  const context = useContext(CardContext);

  const modalStyle = context.modalDisplay ? 'display: none;' : 'display: block; padding-right: 17px;';

  return ( 
    <React.Fragment>
      {context.tempHumid.map((tempHumid, index) => (
          // <div className="col-xl-3 col-md-6 mb-4" key={index}>
          <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(tempHumid.cardviewid)}} key={index}>
            <div className={"shadow h-100 py-2 card border-left-"+tempHumid.colour}>
              <div className="card-body">
                <div className="row no-gutters align-items-center">
                  <div className="col mr-2">
                    <div className="font-weight-bold text text-uppercase mb-1">{tempHumid.sensorname}</div>
                    <div className={context.getSensorReadingStyle(tempHumid.t_hightemp, tempHumid.t_lowtemp, tempHumid.t_tempreading)}>Temperature: {tempHumid.t_tempreading}</div>
                    {context.isHumidityAvalible(tempHumid)}
                  </div>
                  <div className="col-auto">
                    <i className={"fas fa-2x text-gray-300 fa-"+tempHumid.iconname}></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        //  </div>
      ))}

      {context.analog.map((analog, index) => (
        // <div className="col-xl-3 col-md-6 mb-4" key={index}>
          <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(analog.cardviewid)}} key={index} >
            <div className={"shadow h-100 py-2 card border-left-"+analog.colour}>
              <div className="card-body">
                <div className="row no-gutters align-items-center">
                  <div className="col mr-2">
                  <div className="font-weight-bold text text-uppercase mb-1">{analog.sensorname}</div>
                    <div className="h5 mb-0 font-weight-bold text-gray-800">Sensor Reading: {analog.a_analogreading}</div>
                  </div>
                  <div className="col-auto">
                    <i className={"fas fa-2x text-gray-300 fa-"+analog.iconname}></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          // </div>
      ))}
      {context.modalDisplay()}
    </React.Fragment>
  )
}
 
 export default cardRender;