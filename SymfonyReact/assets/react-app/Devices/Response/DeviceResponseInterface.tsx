export interface AddNewDeviceResponse {
    deviceNameID?: number,
    deviceName: string,
    groupID: number,
    roomID: number,
    createdBy: string|number,
    secret: string|null,
    ipAddress: string|null,
    externalIpAddress: string|null,
    roles: string[]
}
