import React, { Component, useContext } from 'react';
import { NavbarContext } from '../contexts/NavbarContext';
import { AddNewDeviceContext } from '../contexts/AddNewDeviceContext';
import { webappURL } from '../Utilities/Common';

const addNewDevice = () => {

    const navBarContext = useContext(NavbarContext);

    const addNewDeviceContext = useContext(AddNewDeviceContext);

    const newDeviceModalContent = addNewDeviceContext.newDeviceModalContent;

    //Refactor has broken link
    const newSensorRoute = webappURL+"device?device-name="+newDeviceModalContent.newDeviceID+"&device-group="+newDeviceModalContent.newDeviceGroup+"&device-room="+newDeviceModalContent.newDeviceRoom;

    return (
        <React.Fragment>
            <div id="" style={navBarContext.addNewDeviceModalToggle !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'}} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-hidden="true">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <form onSubmit={(e) => {addNewDeviceContext.handleNewDeviceFormSubmission(e)}} id="modal-form">
                            <div className="modal-header">
                                <h5 className="modal-title">+Add a new device</h5>
                                <button className="close" onClick={() => {navBarContext.toggleNewDeviceModal()}} type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            {
                            newDeviceModalContent.errors.length > 0 ?                
                                <div className="error-container">
                                    <div className="form-modal-error-box">
                                        <ol>
                                            {newDeviceModalContent.errors.map((error, index) => (
                                                <li key={index} className="form-modal-error-text">{error}</li>
                                            ))}
                                        </ol>
                                    </div>
                                </div>                
                            : null
                            }
                            <div className="modal-body">
                                {newDeviceModalContent.formSubmit !== false ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
                                
                                <label className="large font-weight-bold">Device Name</label>
                                <input type="text" name="deviceName" className="form-control" value={newDeviceModalContent.newDeviceName} onChange={(e) => {addNewDeviceContext.updateNewDeviceModalForm(e)}}></input>
                                
                                <label className="modal-space large font-weight-bold">Group name you would like to add the sensor too</label>                                
                                <select value={newDeviceModalContent.newDeviceGroup} name="deviceGroup" id="deviceGroup" className="form-control" onChange={(e) => {addNewDeviceContext.updateNewDeviceModalForm(e)}} >
                                    {   
                                        navBarContext.userGroupNames.length >= 1
                                        ? navBarContext.userGroupNames.map((groupNames) => (
                                            <option className="form-control" value={groupNames.groupNameID} key={groupNames.groupNameID}>{groupNames.groupName}</option>    
                                            ))
                                        : 
                                        <option>No group names available try to Log Out then back in again</option>
                                    }
                                </select> 

                                <label className="modal-space large font-weight-bold">Which room you would like to add the sensor too</label>                                
                                <select value={newDeviceModalContent.newDeviceRoom} name="deviceRoom" id="deviceRoom" className="form-control" onChange={(e) => {addNewDeviceContext.updateNewDeviceModalForm(e)}} >
                                    {
                                    navBarContext.userRooms.length >= 0 
                                    ? navBarContext.userRooms.map((room, index) => (
                                        <option className="form-control" value={room.roomID} key={index}>{room.room}</option>    
                                        ))
                                    : <option>No Rooms</option>
                                    }
                                </select>
                                {
                                    newDeviceModalContent.deviceSecret !== null ?
                                    <div className="secret-container">
                                    <label className="modal-space large font-weight-bold">This is your devices secret, you will need this when doing an initial setup of your device</label>                                
                                        <div className="secret-box">
                                        <p className="font-weight-bold"> {newDeviceModalContent.deviceSecret}</p>
                                        </div>
                                        <div className="center" style={{paddingTop:"2%"}}>
                                        <a href={newSensorRoute} className="btn-primary modal-submit-center" type="submit" value="submit">Got it!</a>
                                        </div>
                                    </div>
                                    : null
                                }
                            </div>
                            <div className="modal-footer">
                            <button className="btn btn-secondary" type="button" onClick={() => {navBarContext.toggleNewDeviceModal()}} data-dismiss="modal">Cancel</button>
                                <button className="btn btn-primary" type="submit" value="submit">Submit</button>
                            </div>
                        </form>            
                    </div>
                </div>
            </div>
        </React.Fragment>
    )
}

export default addNewDevice;
