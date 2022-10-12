import * as React from 'react';

export function AnnouncementFlashModal(props) {
    const modalShow: boolean = props.modalShow;
    const title: string = props.title

    
    return (
        <div id="" style={modalShow !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'}} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div className="modal-dialog" role="document">
          <div className="modal-content">
            {/* <form onSubmit={(e) => {context.handleSubmissionModalForm(e)}} id="modal-form"> */}
              <div className="modal-header">
                <h5 className="modal-title" style={{textAlign: "centre"}}>{title}</h5>
                  {/* <button className="close" onClick={() => {context.toggleModal()}} type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button> */}
              </div>

              {/* {
                modalStatus.errors.length > 0
                  ?
                    <div className="error-container">
                      <div className="form-modal-error-box">
                        <ol>
                        {
                          modalStatus.errors.map((error, index) => (
                            <li key={index} className="form-modal-error-text">{error}</li>
                          ))
                        }
                        </ol>
                      </div>
                    </div>
                  : null
              } */}

              {/* {
                modalStatus.success.length > 0
                  ?
                    <div className="error-container">
                      <div className="form-modal-success-box">
                        <ol>
                        {
                          modalStatus.success.map((success, index) => (
                            <li key={index} className="form-modal-error-text">Request data accepted for {success}</li>
                          ))
                        }
                        </ol>
                      </div>
                    </div>
                  : null
              } */}

              <div className="modal-success">
                <h4 style={{ textAlign:"center" }}>Submission Made Successfully</h4> 
              </div>
                <React.Fragment>
                  <div className="modal-body">
                    <React.Fragment>
                    {/* {modalStatus.modalSubmit === true ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
                    {
                      modalContent.sensorData.length >= 1
                        ?
                          modalContent.sensorData.map((sensorData, index) => (
                            <div key={index} style={{paddingBottom: "10%"}}>
                              <label className="large font-weight-bold">{capitalizeFirstLetter(sensorData.readingType)} High Reading</label>
                              <br />
                              <input type="text" name={sensorData.readingType+"-high-reading"} className="form-space" value={sensorData.highReading} onChange={(e) => {context.updateModalForm(e, sensorData.readingType)}} /><sup>{sensorData.readingSymbol}</sup>
                              <br />
                              <label className="modal-space large font-weight-bold">{capitalizeFirstLetter(sensorData.readingType)} Low Reading</label>
                              <br />
                              <input type="text" name={sensorData.readingType+"-low-reading"} className="form-space" value={sensorData.lowReading} onChange={(e) => {context.updateModalForm(e, sensorData.readingType)}} /><sup>{sensorData.readingSymbol}</sup>
                              <br />
                              <label className="modal-space large font-weight-bold">{modalSensorType} Temperature Constantly Record Data</label>
                              <select name={sensorData.readingType+"-const-record"} value={sensorData.constRecord} onChange={(e) => {context.updateModalForm(e, sensorData.readingType)}} className="form-control">
                                <option value={false} key="no">No</option>
                                <option value={true} key="yes">Yes</option>
                              </select>
                            </div>
                          ))
                        :
                        null
                    } */}

                    {/* {
                      userSelectionData.userIconSelections.length >= 1
                      ?
                        <React.Fragment>
                          <label className="large font-weight-bold">Icon</label>
                          <br />
                          <select name="card-icon" id="icon-select" value={modalContent.cardIcon.iconID} onChange={(e) => {context.updateModalForm(e)}} className="form-space">
                            {userSelectionData.userIconSelections.map((icon) => (
                              <option key={icon.iconID} value={icon.iconID}>{capitalizeFirstLetter(icon.iconName)}</option>
                            ))}
                          </select>
                          <i className={"fas fa-2x text-gray-300 modal-icon fa-"+modalContent.cardIcon.iconName}></i>
                          <br />
                        </React.Fragment>
                      :
                        null
                    }

                    {
                      userSelectionData.userColourSelections.length >= 1
                      ?
                      <React.Fragment>
                        <label className="modal-space large font-weight-bold">Card Colour</label>
                        <select name="card-colour" value={modalContent.cardColour} onChange={(e) => {context.updateModalForm(e)}} className="form-control">
                          {userSelectionData.userColourSelections.map((colours) => (
                            <option value={colours.colourID} key={colours.colourID}>{capitalizeFirstLetter(colours.colour)}</option>
                          ))}
                        </select>
                      </React.Fragment>
                      :
                        null
                    } */}

                    {/* {
                      userSelectionData.userCardViewSelections.length >= 1
                      ?
                      <React.Fragment>
                        <label className="modal-space large font-weight-bold">Card View</label>
                        <select name="card-view-state" value={modalContent.currentViewState.cardStateID} onChange={(e) => {context.updateModalForm(e)}} className="form-control">
                          {userSelectionData.userCardViewSelections.map((states) => (
                            <option value={states.cardStateID} key={states.cardStateID}>{capitalizeFirstLetter(states.cardState)}</option>
                          ))}
                        </select>
                      </React.Fragment>
                      :
                        null
                    } */}
                    </React.Fragment>
                  </div>
                  {/* <div className="modal-footer">
                    <button className="btn btn-secondary" type="button" onClick={() => {context.toggleModal()}} data-dismiss="modal">Cancel</button>
                    <button className="btn btn-primary" type="submit" value="submit">Submit</button>
                  </div> */}
                </React.Fragment>
            {/* </form> */}
          </div>
        </div>
      </div>
        // <div className="modal fade" id="announcementModal" tabIndex={-1} role="dialog" aria-labelledby="announcementModalLabel" aria-hidden="true">
        //     <div className="modal-dialog" role="document">
        //         <div className="modal-content">
        //             <div className="modal-header">
        //                 <h5 className="modal-title" id="announcementModalLabel">Announcement</h5>
        //                 <button type="button" className="close" data-dismiss="modal" aria-label="Close">
        //                     <span aria-hidden="true">×</span>
        //                 </button>
        //             </div>
        //             <div className="modal-body">
        //                 <p>Announcement</p>
        //             </div>
        //             <div className="modal-footer">
        //                 <button type="button" className="btn btn-secondary" data-dismiss="modal">Close</button>
        //             </div>
        //         </div>
        //     </div>
        // </div>
    );
}