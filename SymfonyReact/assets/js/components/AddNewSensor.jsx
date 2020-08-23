import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

function AddNewSensor(props) {
    const deviceName = useFormInput('');
    const deviceRoom = useFormInput('');
    const deviceGroupName = useFormInput('');

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
            <h1>New Device Setup Form</h1>
                <form>
                    <input placeholder="Device Name" type="text" name="device-name"  {...deviceName}/>
                    <select placeholder="Room/Area" name="device-room">
                        <option>dallase</option>
                    </select>
                    <select placeholder="Group Name" name="device-group-name">
                        <option>Some Groups</option>
                    </select>
                    <button name="submit" action="submit" onClick={handleNewDeviceFormSubmission}>Submit</button>
                </form>
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