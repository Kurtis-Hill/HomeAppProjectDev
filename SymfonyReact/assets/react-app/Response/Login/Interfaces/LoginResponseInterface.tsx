import { UserDataInterface } from './UserDataInterface';

export interface LoginResponseInterface {
    token: string;
    refreshToken: string;
    userData: UserDataInterface;
}
