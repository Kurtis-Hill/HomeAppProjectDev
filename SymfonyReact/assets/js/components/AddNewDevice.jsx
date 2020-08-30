import React, { Component, useContext } from 'react';
import { NavbarContext } from '../contexts/NavbarContext';

const addNewDevice = () => {

    const context = useContext(NavbarContext);

    const newDeviceModalShow = context.addNewDeviceModalToggle;

    const newDeviceModalContent = context.newDeviceModalContent;

    return (
        <React.Fragment>
            {context.addNewDeviceModalLoading !== false ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}

            <div id="" style={context.addNewDeviceModalToggle !== false ? {paddingRight: '17px', display: 'block'} : {display: 'none'}} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <form onSubmit={(e) => {context.handleNewDeviceFormSubmission(e)}} id="modal-form">
                            <div className="modal-header">
                            <h5 className="modal-title">+Add a new device</h5>
                                <button className="close" onClick={() => {context.toggleNewDeviceModal()}} type="button" data-dismiss="modal" aria-label="Close">
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
                                {newDeviceModalContent.formSubmit === true ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
                                
                                <label className="large font-weight-bold">Device Name</label>
                                <input type="text" name="device-name" className="form-control" value={newDeviceModalContent.newDeviceName} onChange={(e) => {context.updateModalForm(e)}}></input>
                                
                                <label className="modal-space large font-weight-bold">Group name you would like to add the sensor too</label>                                
                                <select name="group-name" className="form-control" onChange={(e) => {context.updateModalForm(e)}} >
                                    {newDeviceModalContent.deviceGroupNames.map((groupNames) => (
                                        <option className="form-control" value={groupNames.id} key={groupNames.id}>{groupNames.groupName}</option>
                                    ))}
                                </select> 

                                <label className="modal-space large font-weight-bold">Which room you would like to add the sensor too</label>                                
                                <select name="room-name" className="form-control" onChange={(e) => {context.updateModalForm(e)}} >
                                    {context.userRooms.map((room) => (
                                        <option className="form-control" value={room.r_roomid} key={room.r_roomid}>{room.r_room}</option>
                                    ))}
                                </select>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </React.Fragment>
    )
}

export default addNewDevice;
