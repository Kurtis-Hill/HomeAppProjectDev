import React, { Component, useContext, useState } from 'react';
import { CardContext } from '../contexts/CardContexts';
// import  { CardModal }  from '../components/CardFormModal.jsx';

import { cardFormModal } from '../components/CardFormModal.jsx';



const cardRender = () => {

  const context = useContext(CardContext);

  const modalStyle = context.modalShow !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'};

  return ( 
    <React.Fragment>
      {context.tempHumid.map((tempHumid, index) => (
          // <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(tempHumid.cardviewid, tempHumid.room, tempHumid.sensorname)}} key={index}>
          <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(tempHumid.cardviewid, tempHumid.room, tempHumid.sensorname)}} key={index}>
            <div className={"shadow h-100 py-2 card border-left-"+tempHumid.colour}>
              <div className="card-body">
                <div className="row no-gutters align-items-center">
                  <div className="col mr-2">
                    <div className="font-weight-bold text text-uppercase mb-1">{tempHumid.sensorname}</div>
                    <div className={'h5 mb-0 font-weight-bold '+context.getSensorReadingStyle(tempHumid.t_hightemp, tempHumid.t_lowtemp, tempHumid.t_tempreading)}>Temperature: {tempHumid.t_tempreading}</div>
                    {context.isHumidityAvalible(tempHumid)}
                    <div>@{tempHumid.t_timez.date}</div>
                  </div>
                  <div className="col-auto">
                    <i className={"fas fa-2x text-gray-300 fa-"+tempHumid.iconname}></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
      ))}

      {context.analog.map((analog, index) => (
          // <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(analog.cardviewid, analog.room, analog.sensorname)}} key={index} >
            <div className="col-xl-3 col-md-6 mb-4" onClick={() => {CardModal}} key={index} >
            <div className={"shadow h-100 py-2 card border-left-"+analog.colour}>
              <div className="card-body">
                <div className="row no-gutters align-items-center">
                  <div className="col mr-2">
                  <div className="font-weight-bold text text-uppercase mb-1">{analog.sensorname}</div>
                    <div className="h5 mb-0 font-weight-bold text-gray-800">Sensor Reading: {analog.a_analogreading}</div>
                    <div>@{analog.a_timez.date}</div>
                  </div>
                  <div className="col-auto">
                    <i className={"fas fa-2x text-gray-300 fa-"+analog.iconname}></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
      ))}
      
      {context.modalLoading === true ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}

      <div id="" style={modalStyle} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div className="modal-dialog" role="document">
          <div className="modal-content">
            {context.modalContent}
            <div className="modal-footer">
                <button className="btn btn-secondary" type="button" onClick={() => {context.toggleModal()}} data-dismiss="modal">Cancel</button>
                  <a className="btn btn-primary" href="login.html">Submit</a>
            </div>
          </div>
        </div>
      </div>
    </React.Fragment>
  )
}
 
 export default cardRender;