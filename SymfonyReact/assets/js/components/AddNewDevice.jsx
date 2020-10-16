import React, { Component, useContext } from 'react';
import { NavbarContext } from '../contexts/NavbarContext';
import { AddNewDeviceContext } from '../contexts/AddNewDeviceContext';

const addNewDevice = () => {

    const context = useContext(NavbarContext);

    const addNewDeviceContext = useContext(AddNewDeviceContext);

    const newDeviceModalContent = addNewDeviceContext.newDeviceModalContent;

    const newSensorRoute = "/HomeApp/devices?device-name="+newDeviceModalContent.newDeviceName+"?device-group="+newDeviceModalContent.newDeviceGroup+"?device-room="+newDeviceModalContent.newDeviceRoom;

    return (
        <React.Fragment>
            <div id="" style={addNewDeviceContext.addNewDeviceModalToggle !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'}} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-hidden="true">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <form onSubmit={(e) => {context.addNewDeviceContext(e)}} id="modal-form">
                            <div className="modal-header">
                            <h5 className="modal-title">+Add a new device</h5>
                                <button className="close" onClick={() => {addNewDeviceContext.toggleNewDeviceModal()}} type="button" data-dismiss="modal" aria-label="Close">
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
                                <input type="text" name="device-name" className="form-control" value={newDeviceModalContent.newDeviceName} onChange={(e) => {addNewDeviceContext.updateNewDeviceModalForm(e)}}></input>
                                
                                <label className="modal-space large font-weight-bold">Group name you would like to add the sensor too</label>                                
                                <select name="group-name" id="group-name" className="form-control" onChange={(e) => {addNewDeviceContext.updateNewDeviceModalForm(e)}} >
                                    {context.groupNames.map((groupNames) => (
                                        <option className="form-control" value={groupNames.groupnameid} key={groupNames.groupnameid}>{groupNames.groupname}</option>
                                    ))}
                                </select> 

                                <label className="modal-space large font-weight-bold">Which room you would like to add the sensor too</label>                                
                                <select name="room-name" id="device-room" className="form-control" onChange={(e) => {addNewDeviceContext.updateNewDeviceModalForm(e)}} >
                                    {context.userRooms.map((room) => (
                                        <option className="form-control" value={room.r_roomid} key={room.r_roomid}>{room.r_room}</option>
                                    ))}
                                </select>
                                {
                                    newDeviceModalContent.deviceSecret !== null ?
                                    <div className="secret-container">
                                    <label className="modal-space large font-weight-bold">This is your devices secret, you will need this when doing an initial setup of yout device</label>                                
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
                            <button className="btn btn-secondary" type="button" onClick={() => {addNewDeviceContext.toggleNewDeviceModal()}} data-dismiss="modal">Cancel</button>
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
