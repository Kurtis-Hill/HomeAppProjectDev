import React, { Component, useContext, useState } from 'react';
import { CardContext } from '../contexts/CardContexts';




const cardRender = () => {

  const context = useContext(CardContext);

  const modalStyle = context.modalShow !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'};

  const modalSensorType = context.modalContent.sensorType;

  const secondModalSensorType = context.modalContent.secondSensorType;

  const modalContent = context.modalContent;


  return ( 
    <React.Fragment>
      {context.tempHumid.map((tempHumid, index) => (
        <div className="col-xl-3 col-md-6 mb-4" onClick={() => {context.getCardDataForm(tempHumid.cardviewid, tempHumid.room, tempHumid.sensorname)}} key={index}>
          <div className={"shadow h-100 py-2 card border-left-"+tempHumid.colour}>
            <div className="card-body">
              <div className="row no-gutters align-items-center">
                <div className="col mr-2">
                  <div className="font-weight-bold text text-uppercase mb-1">{tempHumid.sensorname}</div>
                  <div className={'h5 mb-0 font-weight-bold '+context.getSensorReadingStyle(tempHumid.t_hightemp, tempHumid.t_lowtemp, tempHumid.t_tempreading)}>Temperature: {tempHumid.t_tempreading}</div>
                  {context.isHumidityAvalible(tempHumid)}
                  <div className="">@{tempHumid.t_timez.date}</div>
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

        { context.modalContent === undefined ? null :
          <div id="" style={modalStyle} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div className="modal-dialog" role="document">
              <div className="modal-content">

                <form onSubmit={(e) => {context.handleModalForm(e)}} id="modal-form">

                  <div className="modal-header">
                    <h5 className="modal-title">Change {context.modalContent.sensorName}'s Sensor Details</h5>
                        <button className="close" onClick={() => {context.toggleModal()}} type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div className="modal-body">
                    
                      
                    {modalContent.modalSubmit === true ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
                 
                        <label className="large font-weight-bold">{modalContent.sensorType} High Reading</label>
                        <input type="text" name="highReading" className="form-control" defaultValue={modalContent.sensorHighReading} onChange={(e) => {context.updateModalForm(e)}}></input>
                    
                        <label className="modal-space large font-weight-bold">{modalContent.sensorType} Low Reading</label>
                        <input type="text" name="lowReading" className="form-control" defaultValue={modalContent.sensorLowReadings} onChange={(e) => {context.updateModalForm(e)}}></input>

                        {context.modalContent.secondSensorID === null ? null : 
                          <React.Fragment>
                            <label className="modal-space large font-weight-bold">{modalContent.secondSensorType} High Reading</label>
                            <input type="text" name="secondHighReading" className="form-control" defaultValue={modalContent.secondSensorHighReading} onChange={(e) => {context.updateModalForm(e)}}></input>
                        
                            <label className="modal-space large font-weight-bold">{modalContent.secondSensorType} Low Reading</label>
                            <input type="text" name="secondLowReading" className="form-control" defaultValue={context.modalContent.secondSensorLowReading} onChange={(e) => {context.updateModalForm(e)}}></input>
                          </React.Fragment>
                        }
                   

                      
                        <label className="modal-space large font-weight-bold">Icon</label>
                        <br />
                        <select name="icon" value={context.capitalizeFirstLetter(context.modalContent.currentIcon)} onChange={(e) => {context.updateModalForm(e)}} className="form-space">
                            {context.modalContent.icons.map((icons, index) => (
                              <option key={icons.i_iconid}>{context.capitalizeFirstLetter(icons.i_iconname)}</option>
                            ))}
                        </select>
                        <i className={"fas fa-2x text-gray-300 modal-icon fa-"+context.modalContent.currentIcon}></i>
                        <br />
                    
                        <label className="modal-space large font-weight-bold">Card Colour</label>
                        <select value={context.capitalizeFirstLetter(context.modalContent.currentColour)} onChange={(e) => {context.updateModalForm(e)}} name="colour" className="form-control">
                            {context.modalContent.colours.map((colours) => (
                             <option key={colours.c_colourid}>{context.capitalizeFirstLetter(colours.c_shade)}</option>
                            ))}
                           
                        </select>
                        
                        <label className="modal-space large font-weight-bold">Card View</label>
                        <select name="card-view" value={context.capitalizeFirstLetter(context.modalContent.currentState)} onChange={(e) => {context.updateModalForm(e)}} className="form-control">
                            {context.modalContent.states.map((states, index) => (
                              <option key={states.cs_cardstateid}>{context.capitalizeFirstLetter(states.cs_state)}</option>
                            ))}
                        </select>

                        <label className="modal-space large font-weight-bold">{modalSensorType} Constantly Record Data</label>
                        <select value={context.capitalizeFirstLetter(context.modalContent.constRecord)} onChange={(e) => {context.updateModalForm(e)}} name="const-record" className="form-control">
                            <option key="no">No</option>
                            <option key="yes">Yes</option>
                        </select>

                        {context.modalContent.secondSensorID === null ? null : 
                          <React.Fragment>
                            <label className="modal-space large font-weight-bold">{secondModalSensorType} Constantly Record Data</label>
                            <select value={context.capitalizeFirstLetter(context.modalContent.constRecord)} onChange={(e) => {context.updateModalForm(e)}} name="second-const-record" className="form-control">
                                <option key="no">No</option>
                                <option key="yes">Yes</option>
                            </select>
                          </React.Fragment>
                        }

                        <input name="cardViewID" type="hidden" defaultValue={context.modalContent.cardViewID}></input>
                  
                    
                  </div>



                  <div className="modal-footer">
                  
                        <button className="btn btn-secondary" type="button" onClick={() => {context.toggleModal()}} data-dismiss="modal">Cancel</button>
                        <button className="btn btn-primary" type="submit" value="submit">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        }



    </React.Fragment>
  )
}
 
 export default cardRender;


