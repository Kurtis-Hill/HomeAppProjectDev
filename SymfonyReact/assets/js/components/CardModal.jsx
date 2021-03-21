import React, { Component, useContext } from 'react';
import { CardContext } from '../contexts/CardContexts';
import { capitalizeFirstLetter } from '../Utilities/Common';
import { DHT, DallasTemp, Soil } from '../Utilities/SensorsCommon';

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

            {
              modalContent.errors.length > 0 ?                
                <div className="error-container">
                  <div className="form-modal-error-box">
                    <ol>
                    {
                      modalContent.errors.map((error, index) => (
                        <li key={index} className="form-modal-error-text">{error}</li>
                      ))
                    }
                    </ol>
                  </div>
                </div>                
            : null
          }
                  
              {modalContent.submitSuccess === true ? <div className="modal-success"><h4 style={{textAlign:"center"}}>Submission Made Successfully</h4> </div> :
              <React.Fragment>   
                <div className="modal-body">
                  <React.Fragment>   
                  {modalContent.modalSubmit === true ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}   

                  {
              
                    modalContent.sensorData.length >= 1 
                    ? 
                      modalContent.sensorData.map((sensorData, index) => (                        
                        <div key={index} style={{paddingBottom: "10%"}}>
                          
                          <label className="large font-weight-bold">{capitalizeFirstLetter(sensorData.sensorType)} High Reading</label>
                          <br />
                          <input type="text" name={sensorData.sensorType+"HighReading"} className="form-space" value={sensorData.highReading} onChange={(e) => {context.updateModalForm(e, sensorData.sensorType)}}></input><sup>{sensorData.readingSymbol}</sup>
                          <br />
                          <label className="modal-space large font-weight-bold">{capitalizeFirstLetter(sensorData.sensorType)} Low Reading</label>
                          <br />
                          <input type="text" name={sensorData.sensorType+"LowReading"} className="form-space" value={sensorData.lowReading} onChange={(e) => {context.updateModalForm(e, sensorData.sensorType)}}></input><sup>{sensorData.readingSymbol}</sup>
                          <br />
                          <input type="radio" name={sensorData.sensorType+"ConstRecord"} value={sensorData.constRecord} onChange={(e) => {context.updateModalForm(e, sensorData.sensorType)}} className="form-control"></input>
                          <input type="radio" name={sensorData.sensorType+"ConstRecord212"} value={2} onChange={(e) => {context.updateModalForm(e, sensorData.sensorType)}} className="form-control"></input>
                          <label className="modal-space large font-weight-bold">{modalSensorType} Temperature Constantly Record Data</label>
                          <select name={sensorData.sensorType+"ConstRecord"} value={sensorData.constRecord} onChange={(e) => {context.updateModalForm(e, sensorData.sensorType)}} className="form-control">
                            <option value={false} key="no">No</option>
                            <option value={true} key="yes">Yes</option>
                          </select>
                        </div>
                      ))                
                    : 
                    null                      
                  }

                  {
                    modalContent.userColourSelections.length >= 1
                    ?
                      <React.Fragment>
                        <label className="large font-weight-bold">Icon</label>
                        <br />
                        <select id="icon-select" name="cardIcon" value={modalContent.cardIcon.iconID} onChange={(e) => {context.updateModalForm(e)}} className="form-space">
                          {modalContent.userIconSelections.map((icons) => (
                            <option key={icons.iconID} value={icons.iconID}>{capitalizeFirstLetter(icons.iconName)}</option>
                          ))}
                        </select>
                        <i className={"fas fa-2x text-gray-300 modal-icon fa-"+modalContent.cardIcon.iconName}></i>
                        <br />
                      </React.Fragment>                   
                    :
                      null
                  }

                  {
                    modalContent.userColourSelections.length >= 1
                    ?
                    <React.Fragment>
                      <label className="modal-space large font-weight-bold">Card Colour</label>
                      <select value={modalContent.currentColour} onChange={(e) => {context.updateModalForm(e)}} name="cardColour" className="form-control">
                        {modalContent.userColourSelections.map((colours) => (
                          <option value={colours.colourID} key={colours.colourID}>{capitalizeFirstLetter(colours.colour)}</option>
                        ))}
                      </select>
                    </React.Fragment>
                    :
                      null
                  }

                  {
                    modalContent.userCardViewSelections.length >= 1
                    ?
                    <React.Fragment>
                      <label className="modal-space large font-weight-bold">Card View</label>
                      <select name="cardViewState" value={modalContent.currentViewState.cardStateID} onChange={(e) => {context.updateModalForm(e)}} className="form-control">
                        {modalContent.userCardViewSelections.map((states) => (
                          <option value={states.cardStateID} key={states.cardStateID}>{capitalizeFirstLetter(states.state)}</option>
                        ))}
                      </select>
                    </React.Fragment>
                    :
                      null
                  }      
                  </React.Fragment>
                </div>
                <div className="modal-footer">          
                  <button className="btn btn-secondary" type="button" onClick={() => {context.toggleModal()}} data-dismiss="modal">Cancel</button>
                  <button className="btn btn-primary" type="submit" value="submit">Submit</button>
                </div>
              </React.Fragment>
            }   
          </form>       
        </div>
      </div>
    </div>
  </React.Fragment>
  )
}
export default cardModal;
