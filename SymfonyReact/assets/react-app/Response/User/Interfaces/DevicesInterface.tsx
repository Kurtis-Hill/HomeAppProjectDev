export default interface DeviceInterface {
    deviceNameID: number|null;
    deviceName: string;
    deviceSecret: string|null;
    groupNameID: number;
    roomID: number;
    // updatedAt: Date = new DateTime();
    createdBy: number;
    secret: string|null;
    ipAddress: string|null;
    externalIpAddress: string|null;
    roles: Array<string>
}