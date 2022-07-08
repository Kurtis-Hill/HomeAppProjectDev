import React, { useState, useEffect, useContext } from 'react';
import axios from 'axios';
import { getAPIHeader } from '../../../Utilities/APICommon';
import { apiURL } from '../../../Utilities/URLSCommon';

function UpdateUser(props) {

    


    return (
        <form>
        <div className="form-group">
            <input type="first-name" className="form-control"/>
            <input type="last-name" className="form-control"/>
            <input type="email" className="form-control"/>
            <file type="profile-pic" className="form-control"/>
        </div>
        <button type="submit" className="btn btn-primary">Submit</button>
    </form>
    );
}

export default UpdateUser;