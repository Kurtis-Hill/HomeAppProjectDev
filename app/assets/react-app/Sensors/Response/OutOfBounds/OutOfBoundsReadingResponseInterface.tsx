export interface OutOfBoundsReadingResponse {
    sensorReadingID: number;
    sensorReading: number;
    createdAt: string;
    readingType: 'temperature' | 'humidity' | 'analog' | 'latitude';
}

export interface OutOfBoundsQueryPayload {
    payload: OutOfBoundsReadingResponse[];
    title: string;
}
