import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

    // const deviceName = useFormInput('');
    // const deviceRoom = useFormInput('');
    // const deviceGroupName = useFormInput('');
    
    function AddNewSensor(props) {
      //  const deviceName = useFormInput('');
    console.log('clicked');

    const getAllUserFormDetails = async (event) => {

        const getAllUserGroups = await axios.get('/HomeApp/user/group-data')
            .catch(error => {
                console.log('group name error', error.data);
            });

        const getAllUserRooms =  await axios.get('HomeApp/user/room-data')
            .catch(error => {
                console.log('get user room error', error.data);
            });
    }
    
    const handleNewDeviceFormSubmission = async (event) => {

    }

    return (
        <React.Fragment>

<div id="" style={{paddingRight: '17px', display: 'none'}} className="modal-show modal fade show"  tabIndex={-1} role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div className="modal-dialog" role="document">
        <div className="modal-content">
          <form id="modal-form">
            <div className="modal-header">
              <h5 className="modal-title">Change 's Sensor Details</h5>
                <button className="close"  type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <input type="text" name="deviceName" {...this.deviceName} />
            </div>
            </form>
            </div>
            </div>
            </div>
        </React.Fragment>
    );
}

const useFormInput = initialValue => {
    const [value, setvalue] = useState(initialValue);

    const handleChange = e => {
        setvalue(e.target.value);
    }

    return {
        value,
        onChange: handleChange
    }
}

export default AddNewSensor;