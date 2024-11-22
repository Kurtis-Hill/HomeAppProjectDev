import axios from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";

export async function getSingleUserGroupsRequest(groupID: number, responseType?: string) {


    return await axios.get(
        `${apiURL}user-groups/${groupID}`,
        { params: { responseType: responseType ?? 'full' } }
    );
}
