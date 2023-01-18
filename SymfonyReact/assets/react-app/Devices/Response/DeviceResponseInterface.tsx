export interface AddNewDeviceResponse {
    deviceNameID?: number,
    deviceName: string,
    groupNameID: number,
    roomID: number,
    createdBy: string|number,
    secret: string|null,
    ipAddress: string|null,
    externalIpAddress: string|null,
    roles: string[]
}