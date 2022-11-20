import { UserDataInterface } from "../../Login/Interfaces/UserDataInterface";

export interface TokenRefreshResponseInterface {
    token: string;
    refreshToken: string;
    userData: UserDataInterface;
}
