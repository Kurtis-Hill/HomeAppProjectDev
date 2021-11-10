import React, { useState, useEffect, useContext } from 'react';
import axios from 'axios';
import { getAPIHeader } from '../Utilities/APICommon';
import { apiURL } from '../Utilities/URLSCommon';

function AddNewRoom(props) {
    useEffect(() => {
        getUserRooms();
    }, userRooms);

    const [userRooms, setUserRooms] = useState([]);
    const [errors, setErrors] = useState('');

    const [roomsTypes, setRoomTypes] = useState([]);
    const [selectedRoom, setSelectedRoom] = useState('');

    const getUserRooms = async () => {
        try {
            const userRoomsResponse = await axios.get(`${apiURL}/user-rooms`, getAPIHeader());

            setRoomTypes(userRoomsResponse.data);
            setSelectedRoom(userRooms.data[0].roomID);
        } catch (error) {
            setErrors(error.data)
        }
    }
}
export default AddNewRoom;