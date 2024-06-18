import axios from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function getAllUserGroupsRequest(responseType?: string) {
    return await axios.get(
        `${apiURL}user-groups/all`,
        { params: { responseType: responseType } }
    );
}