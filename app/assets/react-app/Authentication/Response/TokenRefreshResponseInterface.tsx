import { UserDataInterface } from "../../User/Response/UserDataInterface";

export interface TokenRefreshResponseInterface {
    token: string;
    refreshToken: string;
    userData: UserDataInterface;
}
