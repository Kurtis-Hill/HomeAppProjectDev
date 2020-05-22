import React, { Component, useContext, useState } from 'react'
import { CardContext } from '../contexts/CardContexts'

const cardRender = () => {

  const context = useContext(CardContext);

  const showModal = context.modalShow !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'};

  return ( 
    <React.Fragment>
      {context.tempHumid.map((tempHumid, index) => (
          <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(tempHumid.cardviewid, tempHumid.room, tempHumid.sensorname)}} key={index}>
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
      ))}

      {context.analog.map((analog, index) => (
          <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(analog.cardviewid, analog.room, analog.sensorname)}} key={index} >
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
      ))}

      {context.modalLoading === true ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
      <div id="logoutModal" style={showModal} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="exampleModalLabel">TITLE</h5>
                            <button className="close" type="button" data-dismiss="modal" aria-label="Close">
                              {/* <span aria-hidden="true" onClick={context.toggleModal()}>×</span> */}
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        
                        <div className="modal-footer">
                            <button className="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                              <a className="btn btn-primary" href="login.html">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
    </React.Fragment>
  )
}
 
 export default cardRender;