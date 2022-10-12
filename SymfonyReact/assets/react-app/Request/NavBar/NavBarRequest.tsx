import axios from 'axios';

import { apiURL } from "../../Common/CommonURLs";
import { getAPIHeader } from "../../Common/APICommon";

import NavBarResponseInterface from "../../Response/NavBar/NavBarResponseInterface";

export async function handleNavBarRequest(): Promise<NavBarResponseInterface> {
    const navBarResponse = await axios.get(
        `${apiURL}navbar/navbar-data`,
        getAPIHeader()
    );        

    return navBarResponse.data.payload;
}