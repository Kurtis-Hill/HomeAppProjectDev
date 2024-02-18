import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import AddNewGroupUserInputInterface from '../../Components/Group/AddNewGroupUserInputInterface';

export async function addNewGroupRequest(addNewGroupInputs: AddNewGroupUserInputInterface): Promise<AxiosResponse> {
    try {
        const addNewGroupResponse: AxiosResponse = await axios.post(
            `${apiURL}user-groups/add`,
            addNewGroupInputs
        );

        return addNewGroupResponse;
    } catch (err) {
        const error = err as Error | AxiosError;
        Promise.reject();
        
        return err;
    }
}