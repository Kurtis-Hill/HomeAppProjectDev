import React, { Component, useContext } from 'react';
import { capitalizeFirstLetter } from '../Utilities/Common';
import { CardContext } from '../contexts/CardContexts';

 const cardModal = () => {

    const context = useContext(CardContext);

    const modalSensorType = context.modalContent.sensorType;

    const secondModalSensorType = context.modalContent.secondSensorType;
    
    const modalContent = context.modalContent;

    return (
    <React.Fragment>
      {context.modalLoading !== false ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
      
      <div id="" style={context.modalShow !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'}} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div className="modal-dialog" role="document">
          <div className="modal-content">
            <form onSubmit={(e) => {context.handleSubmissionModalForm(e)}} id="modal-form">
              <div className="modal-header">
                <h5 className="modal-title">Change {modalContent.sensorName}'s Sensor Details</h5>
                    <button className="close" onClick={() => {context.toggleModal()}} type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
              </div>
              <div className="modal-body">
                {modalContent.modalSubmit === true ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
              
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
              </div>
              <div className="modal-footer">          
                <button className="btn btn-secondary" type="button" onClick={() => {context.toggleModal()}} data-dismiss="modal">Cancel</button>
                <button className="btn btn-primary" type="submit" value="submit">Submit</button>
              </div>
            </form>       
          </div>
        </div>
      </div>
    </React.Fragment>
    )
}
export default cardModal;