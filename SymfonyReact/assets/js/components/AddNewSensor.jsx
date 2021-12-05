import React, { useState, useEffect, useContext } from 'react';
import axios from 'axios';
import { getAPIHeader } from '../Utilities/APICommon';
import { apiURL } from '../Utilities/URLSCommon';

import { CardContext } from '../contexts/CardContexts';

function AddNewSensor(props) {

    useEffect(() => {
        getSensorTypes();
    }, [sensorTypes]);

    const cardContext = useContext(CardContext);

    const [successMessage, setSuccessMessage] = useState(false);
    const [loading, setLoading] = useState(false);
    const [modalToggle, setModalToggle] = useState(false);
    const [errors, setErrors] = useState([]);
    const [sensorTypes, setSensorTypes] = useState([]);
    const [selectedSensorTypes, setSelectedSensorTypes] = useState('');

    const sensorName = useFormInput('');

    const toggleModal = () => {
        setModalToggle(!modalToggle);
        setSuccessMessage(false);
    }

    const getSensorTypes = async () => {
        try {
            const sensorTypeResponse = await axios.get(`${apiURLsensors}/types`, getAPIHeader());

            if (sensorTypeResponse.data) {
                setSelectedSensorTypes(sensorTypeResponse.data[0].sensorTypeID);
                setSensorTypes(sensorTypeResponse.data);
            }
        } catch (error) {
            setErrors(['something has gone wrong getting form field data, unexpected response'])
        }
    }

    const toggleModalOn = () => {
        setModalToggle(true);
    }

    const updateSensorType = (selectedType) => {
        setSelectedSensorTypes(selectedType.target.value)
    }

    const handleFormSubmission = async (e) => {
        e.preventDefault();

        setErrors([]);
        setLoading(true);

        const deviceName = new URLSearchParams(window.location.search).get('device-id');

        const jsonRequestData = {
            'deviceNameID' : deviceName,
            'sensorTypeID' : selectedSensorTypes,
            'sensorName' : sensorName.value,
        }

        try {
            const addNewSensorRequest = await axios.post(
                `${apiURL}sensors/add-new-sensor`,
                JSON.stringify(jsonRequestData),
                getAPIHeader()
            );
            if (addNewSensorRequest.status === 201) {
                setLoading(false);
                setSuccessMessage(true);
                setSelectedSensorTypes(sensorTypes[0].sensorTypeID);
                setTimeout(() =>
                    toggleModal(), 1500
                );
            } else {
                setLoading(false);
                setErrors(`unexpected response`);
            }
        } catch (error) {
            const errors = error.response.data.errors;

            setSuccessMessage(false);
            setLoading(false);
            setSelectedSensorTypes(sensorTypes[0].sensorTypeID);
            if (error.response.status === 400) {
                setErrors(errors);
            }
            if (error.response.status === 500) {
                alert(`Something went wrong try refreshing the browser ${data}`);
            }
        }
    }

    return (
        <React.Fragment>
            {
                cardContext.cardData !== null
                    ?
                        <React.Fragment>
                            <div className="col-xl-3 col-md-6 mb-4">
                                <div className="shadow h-100 py-2 card">
                                    <div className="card-body hover" onClick={toggleModalOn}>
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
                                                errors.length > 0
                                                ?
                                                    <div className="error-container">
                                                        <div className="form-modal-error-box">
                                                            <ol>
                                                            {
                                                                errors.map((error, index) => (
                                                                    <li key={index} className="form-modal-error-text">{error}</li>
                                                                ))
                                                            }
                                                            </ol>
                                                        </div>
                                                    </div>
                                                :
                                                    null
                                            }
                                            {
                                                successMessage !== false ? <div className="modal-success"><h1 style={{textAlign:"center"}}>Success</h1></div> :

                                                <React.Fragment>
                                                    <div className="modal-body">
                                                        {loading !== false ? <div className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
                                                        <label className="large font-weight-bold">New Sensor Name</label>
                                                        <input type="text" className="form-space form-control" name="sensor-name" {...sensorName}></input>
                                                        <label className="modal-space large font-weight-bold">Sensor Type</label>
                                                        <select onChange={updateSensorType} name="sensor-type"  className="form-control">
                                                            {
                                                                sensorTypes.map((sensorTypeData) =>(
                                                                    <option className="form-control" value={sensorTypeData.sensorTypeID} key={sensorTypeData.sensorTypeID}>{sensorTypeData.sensorType}</option>
                                                                ))
                                                            }
                                                        </select>
                                                    </div>
                                                    <div className="modal-footer">
                                                        <button className="btn btn-secondary" type="button" onClick={toggleModal} data-dismiss="modal">Cancel</button>
                                                        <button className="btn btn-primary" type="submit" value="submit">Submit</button>
                                                    </div>
                                            </React.Fragment>
                                            }
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </React.Fragment>
                        :
                            null

            }

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
