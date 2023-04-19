import React, { useState, useEffect, useContext } from 'react';
import axios from 'axios';
import { getAPIHeader } from '../../../Utilities/APICommon';
import { apiURL } from '../../../Utilities/URLSCommon';

function AddNewRoom(props) {
    useEffect(() => {
        getUserGroups();
    }, [groups, errors, success]);

    const userRoom = useFormInput('');

    const [selectedGroup, setSelectedGroup] = useState('');
    const [groups, setGroups] = useState([]);
    const [errors, setErrors] = useState([]);
    const [success, setSuccess] = useState(false);

    const getUserGroups = async () => {
        try {
            const userGroupsResponse = await axios.get(`${apiURL}user-groups/all`, getAPIHeader());
            if (userGroupsResponse.data && Array.isArray(userGroupsResponse.data.payload)) {
                const payload = userGroupsResponse.data.payload; 
                setGroups(payload);
                setSelectedGroup(payload[0].groupID);
                setErrors([]);
            }
        } catch (error) {
            const statusCode = error.status;

            if (statusCode === 500) {
                setErrors(["Server error, try again if the problem persists log out and back in again"]);
            }
            if (statusCode === 400) {
                setErrors(error.response.data.errors);
            }
            else {
                setErrors(["unexpected response while getting user groups, log back out and in again"]);
            }
        }
    }

    const handleNewRoomFormSubmission = async (event) => {
        event.preventDefault();
        try {
            const newRoomResponse = await axios.post(`${apiURL}user-rooms/add`, {
                'roomName': userRoom.value,
                'groupID': selectedGroup
            }, getAPIHeader());
            
            setErrors([]);        
            if (newRoomResponse.status === 201) {
                setSuccess(true);
            }
        } catch (error) {
            setSuccess(false);
            const statusCode = error.status;
            if (statusCode === 500) {
                setErrors(["Server error, try again if the problem persists log out and back in again"]);
            } else {              
                if (Array.isArray((error.response.data.errors))) {
                    setErrors(error.response.data.errors);
                } else {
                    setErrors(['Something went wrong']);
                }
            }
        }
    }

    const updateGroup = (selectedGroup) => {
        setSelectedGroup(selectedGroup.target.value);
    }

    return (
        <React.Fragment>
            <h5 className="modal-title">Add A New Room</h5>
            {
                errors && errors.length > 0
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
                success === true 
                    ? 
                        <div className='success-container'>
                            <div className='form-modal-success-box'>
                                <ol>
                                    <li style={{"listStyle":"none", "color": "white"}}><h5>New room has been added</h5></li>
                                </ol>
                            </div>
                        </div>  
                    : 
                        null
            }                    
            <form onSubmit={handleNewRoomFormSubmission}>
                <div className="form-group">
                    <input type="room" className="form-control" aria-describedby="emailHelp" placeholder="Enter room name" {...userRoom}/>
                </div>
                <select value={selectedGroup} name="device-group" id="device-group" className="form-control" onChange={updateGroup} >
                    {
                        groups.length >= 1
                            ? groups.map((group) => (
                                <option className="form-control" value={group.groupID} key={group.groupID}>{group.groupName}</option>
                            ))
                            :
                            <option>No group names available try to Log Out then back in again</option>
                    }
                </select>
                <button type="submit" className="btn btn-primary">Submit</button>
            </form>
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

export default AddNewRoom;
