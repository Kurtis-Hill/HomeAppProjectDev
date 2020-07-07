import React, { Component, useContext } from 'react';
import { capitalizeFirstLetter } from '../Utilities/Common';
import { CardContext } from '../contexts/CardContexts';

export const cardModal = (modalContent, updateModalForm) => {

    const context = useContext(CardContext);

    const modalSensorType = modalContent.sensorType;

    const secondModalSensorType = modalContent.secondSensorType;

    return (
    <React.Fragment>
    {modalContent.cardViewID === null ? <p>Submission Made</p> :
        <React.Fragment>
          <label className="large font-weight-bold">{modalContent.sensorType} High Reading</label>
          <input type="text" name="highReading" className="form-control" value={modalContent.sensorHighReading} onChange={(e) => {context.updateModalForm(e)}}></input>

          <label className="modal-space large font-weight-bold">{modalContent.sensorType} Low Reading</label>
          <input type="text" name="lowReading" className="form-control" value={modalContent.sensorLowReading} onChange={(e) => {context.updateModalForm(e)}}></input>

          {modalContent.secondSensorID ===  null || modalContent.secondSensorID === undefined ? null : 
            <React.Fragment>
              <label className="modal-space large font-weight-bold">{modalContent.secondSensorType} High Reading</label>
              <input type="text" name="secondHighReading" className="form-control" value={modalContent.secondSensorHighReading} onChange={(e) => {context.updateModalForm(e)}}></input>
          
              <label className="modal-space large font-weight-bold">{modalContent.secondSensorType} Low Reading</label>
              <input type="text" name="secondLowReading" className="form-control" value={modalContent.secondSensorLowReading} onChange={(e) => {context.updateModalForm(e)}}></input>
            </React.Fragment>
          }
                      
          <label className="modal-space large font-weight-bold">Icon</label>
          <br />
          <select id="icon-select" name="icon" value={modalContent.iconID} onChange={(e) => {context.updateModalForm(e)}} className="form-space">
              {modalContent.icons.map((icons, index) => (
                <option value={icons.i_iconid} key={icons.i_iconid}>{capitalizeFirstLetter(icons.i_iconname)}</option>
              ))}
          </select>
          <i className={"fas fa-2x text-gray-300 modal-icon fa-"+modalContent.currentIcon}></i>
          <br />
      
          <label className="modal-space large font-weight-bold">Card Colour</label>
          <select value={modalContent.currentColour} onChange={(e) => {context.updateModalForm(e)}} name="cardColour" className="form-control">
              {modalContent.colours.map((colours) => (
                <option value={colours.c_colourid} key={colours.c_colourid}>{capitalizeFirstLetter(colours.c_shade)}</option>
              ))}
          </select>
          
          <label className="modal-space large font-weight-bold">Card View</label>
          <select name="cardViewState" value={modalContent.currentState} onChange={(e) => {context.updateModalForm(e)}} className="form-control">
              {modalContent.states.map((states, index) => (
                <option value={states.cs_cardstateid} key={states.cs_cardstateid}>{capitalizeFirstLetter(states.cs_state)}</option>
              ))}
          </select>

          <label className="modal-space large font-weight-bold">{modalSensorType} Constantly Record Data</label>
          <select name="constRecord" value={modalContent.constRecord} onChange={(e) => {context.updateModalForm(e)}} className="form-control">
              <option value={false} key="no">No</option>
              <option value={true} key="yes">Yes</option>
          </select>

          {!modalContent.secondSensorID || modalContent.secondSensorID === undefined ? null : 
            <React.Fragment>
              <label className="modal-space large font-weight-bold">{secondModalSensorType} Constantly Record Data</label>
              <select name="secondConstRecord" value={modalContent.secondConstRecord} onChange={(e) => {context.updateModalForm(e)}}  className="form-control">
                  <option value={false} key="no">No</option>
                  <option value={true} key="yes">Yes</option>
              </select>
            </React.Fragment>
          }    
        </React.Fragment>
        }
    </React.Fragment>
    )
}

