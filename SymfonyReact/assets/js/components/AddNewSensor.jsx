import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { apiURL, webappURL } from '../Utilities/Common';

function AddNewSensor(props) {

    const [modalToggle, setModalToggle] = useState(false);
    const [errors, setErrors] = useState([]);
    
    const sensorName = useFormInput('');
    const sensorType = useFormInput('');
      
    const toggleModal = () => {
        setModalToggle(!modalToggle);
    }

    const handleFormSubmission = e => {
        e.preventDefault();

        const roomID = new URLSearchParams(window.location.search).get('device-room');
        const groupID = new URLSearchParams(window.location.search).get('device-group');

        axios.post(apiURL+'sensors/submit-form-data', [sensorName, sensorType, groupID, roomID])
        .then(response => {
            console.log('sensor form submit response', response.data);
        })
        .catch(err => {
            setErrors(err.data.errors);
        });
       
    }


    return (
        <React.Fragment>
            <div className="col-xl-3 col-md-6 mb-4">
                <div className="shadow h-100 py-2 card">
                    <div className="card-body hover" onClick={toggleModal}>
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
                        <form id="modal-form" onSubmit={handleFormSubmission}>
                            <div className="modal-header">
                                <h5 className="modal-title">Add A New Sensor</h5>
                                <button onClick={toggleModal} className="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            {
                                errors.length > 0 ?                
                                    <div className="error-container">
                                    <div className="form-modal-error-box">
                                        <ol>
                                        {modalContent.errors.map((error, index) => (
                                        <li key={index} className="form-modal-error-text">{error}</li>
                                        ))}
                                        </ol>
                                    </div>
                                    </div>                
                                : null
                            }
                                <div className="modal-body">
                                    <label className="large font-weight-bold">New Sensor Name</label>
                                    <input type="text" className="form-space form-control" name="deviceName" {...sensorName}></input>
                                    <label className="modal-space large font-weight-bold">Sensor Type</label>
                                    <select { ...sensorType } className="form-control">
                                        <option className="form-control">DHT</option>
                                        <option className="form-control">Dallas</option>
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
        console.log(e.target.value);
    }
    return {
        value,
        onChange: handleChange
    }
}

export default AddNewSensor;