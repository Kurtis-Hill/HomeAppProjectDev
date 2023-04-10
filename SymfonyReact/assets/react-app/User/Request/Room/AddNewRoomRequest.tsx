import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import AddNewRoomUserInputInterface from '../../Components/AddNewRoomUserInputInterface';

export async function addNewRoomRequest(addNewRoomInputs: AddNewRoomUserInputInterface): Promise<AxiosResponse> {
    try {
        const addNewRoomResponse: AxiosResponse = await axios.post(
            `${apiURL}user-rooms/add`,
            addNewRoomInputs
        );

        if (addNewRoomResponse.status) {
            console.log('mee twoo', addNewRoomResponse);
        }
        if (addNewRoomResponse.status === 201) {
            return addNewRoomResponse;
        } else {
            // throw Error('Error status not expected add new room response');
        }

    } catch (err) {
        const error = err as Error | AxiosError;
        console.log('mee twoo22', error)
        Promise.reject();
    }
}