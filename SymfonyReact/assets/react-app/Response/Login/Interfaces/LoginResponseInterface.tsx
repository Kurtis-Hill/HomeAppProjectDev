import UserInterface from './UserDataInterface';

export interface LoginResponseInterface {
    userInputs: {
        token: string;
        refreshToken: string;
        userData: UserInterface;
    }
}
