import axios, { AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import { AddNewGroupUserInput } from '../../Components/Group/AddNewGroupForm';

export async function addNewGroupRequest(addNewGroupInputs: AddNewGroupUserInput): Promise<AxiosResponse> {
    return await axios.post(
        `${apiURL}user-groups/add`,
        addNewGroupInputs
    );
}