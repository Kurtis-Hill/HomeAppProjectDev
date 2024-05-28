export default interface AddNewDeviceUserInputsInterface {
    deviceName: string;
    devicePassword: string;
    devicePasswordConfirm: string;
    deviceGroup: number
    deviceRoom: number;
    deviceIPAddress?: string;
}