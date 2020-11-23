import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { apiURL } from '../Utilities/Common';

    // const deviceName = useFormInput('');
    // const deviceRoom = useFormInput('');
    // const deviceGroupName = useFormInput('');
    
function AddNewSensor(props) {
      //  const deviceName = useFormInput('');
    const [modalToggle, setModalToggle] = useState(false);
    
    const sensorName = useFormInput('');
    const sensorType = useFormInput('');
    const groupID = useFormInput('');
      
    const toggleModal = () => {
        setModalToggle(false);
    }
    const openModal = async () => {
        console.log('clicked');
        setModalToggle(true);

        console.log(modalToggle);
    }

    const getSensorFormOptions = await axios.get(apiURL);

    

    // const getAllUserFormDetails = async (event) => {

    //     const getAllUserGroups = await axios.get('/HomeApp/user/group-data')
    //         .catch(error => {
    //             console.log('group name error', error.data);
    //         });

    //     const getAllUserRooms =  await axios.get('HomeApp/user/room-data')
    //         .catch(error => {
    //             console.log('get user room error', error.data);
    //         });
    // }
    
    // const handleNewDeviceFormSubmission = async (event) => {

    // }





    return (
        <React.Fragment>
            <div className="col-xl-3 col-md-6 mb-4">
                <div className="shadow h-100 py-2 card border-left-primary">
                    <div className="card-body hover" onClick={openModal}>
                        <div className="row no-gutters align-items-center">
                            <div className="col mr-2">
                                <div className="d-flex font-weight-bold text text-uppercase mb-1">Add A Sensor</div>
                            </div>
                            <div className="col-auto">
                                <i className="fas fa-2x text-gray-300 fa-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 

            
            <div style={ modalToggle === true ? {paddingRight: '17px', display: 'block'} : {display: 'none'} } className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <form id="modal-form">
                            <div className="modal-header">
                                <h5 className="modal-title">Add A New Sensor</h5>
                                <button onClick={toggleModal} className="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                                <div className="modal-body">
                                    <label className="large font-weight-bold">New Sensor Name</label>
                                    <input type="text" className="form-space form-control" name="deviceName" {...sensorName}></input>
                                    <label className="modal-space large font-weight-bold">Sensor Type</label>
                                    <select className="form-control">
                                        <option className="form-control">DHT</option>
                                    </select>
                                    <label className="modal-space large font-weight-bold">Group Name</label>
                                    <select className="form-control">
                                        <option className="form-control">admin</option>
                                    </select>
                                </div>
                                <div className="modal-footer">          
                                    <button className="btn btn-secondary" type="button" onClick={toggleModal} data-dismiss="modal">Cancel</button>
                                    <button className="btn btn-primary" type="submit" value="submit">Submit</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
             
        </React.Fragment>
    );
}

const useFormInput = initialValue => {
    const [value, setValue] = useState(initialValue);

    const handleChange = e => {
        setValue(e.target.value);
    }

    return {
        value,
        onChange: handleChange
    }
}

export default AddNewSensor;