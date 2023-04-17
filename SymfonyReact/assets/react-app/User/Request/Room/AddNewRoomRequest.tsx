import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import AddNewRoomUserInputInterface from '../../Components/Room/AddNewRoomUserInputInterface';

export async function addNewRoomRequest(addNewRoomInputs: AddNewRoomUserInputInterface): Promise<AxiosResponse> {
    try {
        const addNewRoomResponse: AxiosResponse = await axios.post(
            `${apiURL}user-rooms/add`,
            addNewRoomInputs
        );

        return addNewRoomResponse;
    } catch (err) {
        const error = err as Error | AxiosError;
        console.log('mee twoo22', error)
        Promise.reject();
        
        return err;
    }
}