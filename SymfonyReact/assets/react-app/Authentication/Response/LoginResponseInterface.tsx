import { UserDataInterface } from '../../User/Response/UserDataInterface';
export interface LoginResponseInterface {
    token: string;
    refreshToken: string;
    userData: UserDataInterface;
}
