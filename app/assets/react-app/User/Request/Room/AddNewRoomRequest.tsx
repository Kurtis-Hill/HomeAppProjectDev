import axios, {AxiosError, AxiosResponse} from 'axios';

import { apiURL } from "../../../Common/URLs/CommonURLs";
import { AddNewRoomUserInput } from '../../Components/Room/AddNewRoomForm';

export async function addNewRoomRequest(addNewRoomInputs: AddNewRoomUserInput): Promise<AxiosResponse> {
    return await axios.post(
        `${apiURL}user-rooms/add`,
        addNewRoomInputs
    );
}