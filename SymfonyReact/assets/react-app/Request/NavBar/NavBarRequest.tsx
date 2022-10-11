import axios from 'axios';

import { apiURL } from "../../Common/CommonURLs";

import NavBarResponseInterface from "../../Response/NavBar/NavBarResponseInterface";

import { getAPIHeader } from "../../Common/APICommon";

export async function handleNavBarRequest(): Promise<NavBarResponseInterface> {
    const navBarResponse = await axios.get(
        `${apiURL}navbar/navbar-data`,
        getAPIHeader()
        );

    // if (navBarResponse.status === 401) {
    //     console.log('worked')
    // }
    console.log('navbar', navBarResponse);
        

    return navBarResponse.data.payload;
}