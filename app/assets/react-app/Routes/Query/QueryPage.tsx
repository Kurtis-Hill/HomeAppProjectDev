import * as React from 'react';
import { useState, useEffect } from 'react';

export default function QueryPage() {

    const [users, setUsers] = useState([]);

    useEffect(() => {
        // fetch('http://localhost:3000/users')
        //     .then(response => response.json())
        //     .then(data => setUsers(data));
    }, []);

    return (
        <>
            <h1>Query Page</h1>
            <span>Select User to search</span>
            <form>
                <select>
                    <option value={"user"}>User</option>
                    <option value={"device"}>Device</option>
                </select>
            </form>
        </>

    )
}
